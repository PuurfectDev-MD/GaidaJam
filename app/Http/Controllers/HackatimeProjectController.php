<?php

namespace App\Http\Controllers;

use App\Models\HackatimeProject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HackatimeProjectController extends Controller
{
    public function syncFromApi(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (empty($user->hackatime_access_token)) {
            $message = 'Connect your Hackatime account before syncing projects.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return redirect()->route('projects.index')->with('error', $message);
        }

        $response = $this->callProjectsEndpoint($user);

        if ($response->status() === 401) {
            if ($this->refreshAccessToken($user)) {
                $response = $this->callProjectsEndpoint($user);
            }

            if ($response->status() === 401) {
                $message = 'Hackatime authorization expired. Please reconnect your Hackatime account.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $message,
                        'reauth_url' => route('auth.hackatime'),
                    ], 401);
                }

                return redirect()->route('projects.index')->with('error', $message);
            }
        }

        if (!$response->successful()) {
            $message = 'Failed to fetch projects from Hackatime API.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 502);
            }

            return redirect()->route('projects.index')->with('error', $message);
        }

        $projects = $this->normalizeProjectsPayload($response->json());
        $syncedCount = 0;

        foreach ($projects as $projectData) {
            $name = trim((string) ($projectData['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            HackatimeProject::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => Str::limit($name, 255, ''),
                ],
                [
                    'external_id' => $projectData['external_id'],
                    'url' => $projectData['url'],
                ]
            );

            $syncedCount++;
        }

        $message = "Synced {$syncedCount} Hackatime projects.";

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'hackatime_projects' => HackatimeProject::where('user_id', $user->id)->orderBy('name')->get(),
            ]);
        }

        return redirect()->route('projects.index')->with('success', $message);
    }

    public function index()
    {
        return response()->json([
            'hackatime_projects' => HackatimeProject::where('user_id', Auth::id())
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('hackatime_projects', 'name')->where(fn ($query) => $query->where('user_id', Auth::id())),
            ],
            'external_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('hackatime_projects', 'external_id')
                    ->where(fn ($query) => $query->where('user_id', Auth::id()))
                    ->whereNotNull('external_id'),
            ],
            'url' => 'nullable|url|max:2048',
        ]);

        $hackatimeProject = HackatimeProject::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Hackatime project created successfully.',
            'hackatime_project' => $hackatimeProject,
        ], 201);
    }

    public function update(Request $request, HackatimeProject $hackatimeProject)
    {
        abort_unless($hackatimeProject->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('hackatime_projects', 'name')
                    ->where(fn ($query) => $query->where('user_id', Auth::id()))
                    ->ignore($hackatimeProject->id),
            ],
            'external_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('hackatime_projects', 'external_id')
                    ->where(fn ($query) => $query->where('user_id', Auth::id()))
                    ->whereNotNull('external_id')
                    ->ignore($hackatimeProject->id),
            ],
            'url' => 'nullable|url|max:2048',
        ]);

        $hackatimeProject->update($validated);

        return response()->json([
            'message' => 'Hackatime project updated successfully.',
            'hackatime_project' => $hackatimeProject->fresh(),
        ]);
    }

    public function destroy(HackatimeProject $hackatimeProject)
    {
        abort_unless($hackatimeProject->user_id === Auth::id(), 403);

        $hackatimeProject->delete();

        return response()->json([
            'message' => 'Hackatime project deleted successfully.',
        ]);
    }

    private function callProjectsEndpoint(User $user): Response
    {
        $includeArchived = filter_var(config('services.hackatime.include_archived_projects'), FILTER_VALIDATE_BOOL);

        return Http::acceptJson()
            ->withToken($user->hackatime_access_token)
            ->timeout(15)
            ->get((string) config('services.hackatime.projects_url'), [
                'include_archived' => $includeArchived ? 'true' : 'false',
            ]);
    }

    private function refreshAccessToken(User $user): bool
    {
        if (empty($user->hackatime_refresh_token)) {
            return false;
        }

        $clientId = (string) config('services.hackatime.client_id');
        $clientSecret = (string) config('services.hackatime.client_secret');

        if ($clientId === '' || $clientSecret === '') {
            return false;
        }

        $response = Http::asForm()->post((string) config('services.hackatime.token_url'), [
            'grant_type' => 'refresh_token',
            'refresh_token' => $user->hackatime_refresh_token,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        if (!$response->successful()) {
            return false;
        }

        $payload = $response->json();

        $user->hackatime_access_token = $payload['access_token'] ?? $user->hackatime_access_token;
        $user->hackatime_refresh_token = $payload['refresh_token'] ?? $user->hackatime_refresh_token;
        $user->hackatime_token_expires_at = isset($payload['expires_in'])
            ? now()->addSeconds((int) $payload['expires_in'])
            : $user->hackatime_token_expires_at;
        $user->save();

        return true;
    }

    private function normalizeProjectsPayload(array $payload): array
    {
        $items = [];

        if (isset($payload['data']) && is_array($payload['data'])) {
            $items = $payload['data'];
        } elseif (isset($payload['projects']) && is_array($payload['projects'])) {
            $items = $payload['projects'];
        } elseif (array_is_list($payload)) {
            $items = $payload;
        }

        return collect($items)->map(function ($item) {
            $name = $item['name'] ?? $item['project'] ?? null;
            $externalId = $item['id'] ?? $item['project_id'] ?? $item['slug'] ?? null;
            $url = $item['url'] ?? $item['html_url'] ?? null;

            return [
                'name' => $name,
                'external_id' => $externalId ? Str::limit((string) $externalId, 255, '') : null,
                'url' => $url ? Str::limit((string) $url, 2048, '') : null,
            ];
        })->all();
    }
}
