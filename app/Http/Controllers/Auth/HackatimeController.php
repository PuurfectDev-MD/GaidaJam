<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HackatimeController extends Controller
{
    public function redirect(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Authentication session expired. Please login again.');
        }

        $clientId = (string) config('services.hackatime.client_id');
        $clientSecret = (string) config('services.hackatime.client_secret');

        if ($clientId === '' || $clientSecret === '') {
            return redirect()->route('projects.index')->with('error', 'Hackatime OAuth is not configured by the application.');
        }

        $state = Str::random(40);
        $request->session()->put('hackatime_oauth_state', $state);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => config('services.hackatime.redirect'),
            'response_type' => 'code',
            'scope' => (string) config('services.hackatime.scopes', 'read'),
            'state' => $state,
        ]);

        $authorizeUrl = rtrim((string) config('services.hackatime.authorize_url'), '/');

        return redirect()->away("{$authorizeUrl}?{$query}");
    }

    public function callback(Request $request)
    {
        try {
            $expectedState = $request->session()->pull('hackatime_oauth_state');
            $receivedState = $request->query('state');

            if (!$expectedState || !$receivedState || !hash_equals($expectedState, $receivedState)) {
                return redirect()->route('projects.index')->with('error', 'Hackatime OAuth state is invalid.');
            }

            if ($request->has('error')) {
                $message = $request->query('error_description', 'Hackatime authorization was denied.');
                return redirect()->route('projects.index')->with('error', $message);
            }

            $code = $request->query('code');

            if (!$code) {
                return redirect()->route('projects.index')->with('error', 'Hackatime authorization code is missing.');
            }

            /** @var User|null $user */
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Authentication session expired. Please login again.');
            }

            $clientId = (string) config('services.hackatime.client_id');
            $clientSecret = (string) config('services.hackatime.client_secret');

            if ($clientId === '' || $clientSecret === '') {
                return redirect()->route('projects.index')->with('error', 'Hackatime OAuth is not configured by the application.');
            }

            $tokenResponse = Http::asForm()->post((string) config('services.hackatime.token_url'), [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('services.hackatime.redirect'),
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

            if (!$tokenResponse->successful()) {
                return redirect()->route('projects.index')->with('error', 'Failed to exchange Hackatime token.');
            }

            $payload = $tokenResponse->json();

            if (empty($payload['access_token'])) {
                return redirect()->route('projects.index')->with('error', 'Hackatime did not return an access token.');
            }

            $user->hackatime_access_token = $payload['access_token'] ?? null;
            $user->hackatime_refresh_token = $payload['refresh_token'] ?? null;
            $user->hackatime_token_expires_at = isset($payload['expires_in'])
                ? now()->addSeconds((int) $payload['expires_in'])
                : null;

            $meResponse = Http::acceptJson()
                ->withToken($user->hackatime_access_token)
                ->get((string) config('services.hackatime.me_url'));

            if ($meResponse->successful()) {
                $profile = $meResponse->json();
                $user->hackatime_user_id = $profile['data']['id'] ?? $profile['id'] ?? $user->hackatime_user_id;
            }

            $user->save();

            return redirect()->route('projects.index')->with('success', 'Hackatime account connected.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('projects.index')->with('error', 'Hackatime login failed. Please try again.');
        }
    }

    public function disconnect()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Authentication session expired. Please login again.');
        }

        $user->hackatime_access_token = null;
        $user->hackatime_refresh_token = null;
        $user->hackatime_token_expires_at = null;
        $user->hackatime_user_id = null;
        $user->save();

        return redirect()->route('projects.index')->with('success', 'Hackatime disconnected successfully.');
    }
}