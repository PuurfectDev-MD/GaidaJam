<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\HackatimeProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        return Inertia::render('Projects/Index', [
            'projects' => Project::where('user_id', Auth::id())
                ->with('hackatimeProjects')
                ->get(),
            'hackatimeProjects' => HackatimeProject::where('user_id', Auth::id())
                ->orderBy('name')
                ->get(),
            'hackatimeConnected' => !empty($user?->hackatime_access_token),
            'hackatimeConfigured' => !empty(config('services.hackatime.client_id'))
                && !empty(config('services.hackatime.client_secret')),
            'hackatimeAuthUrl' => route('auth.hackatime'),
            'hackatimeDisconnectRoute' => route('auth.hackatime.disconnect'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'hackatime_project_ids' => 'array',
            'hackatime_project_ids.*' => 'integer|exists:hackatime_projects,id',
        ]);

        // Add the authenticated user's ID
        $validated['user_id'] = Auth::id();

        $hackatimeProjectIds = collect($validated['hackatime_project_ids'] ?? [])->values();
        unset($validated['hackatime_project_ids']);

        $authorizedCount = HackatimeProject::where('user_id', Auth::id())
            ->whereIn('id', $hackatimeProjectIds)
            ->count();

        abort_unless($authorizedCount === $hackatimeProjectIds->count(), 403);

        $project = Project::create($validated);
        $project->hackatimeProjects()->sync($hackatimeProjectIds->all());

        return redirect()->route('projects.index')->with('success', 'Project added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        abort_unless($project->user_id === Auth::id(), 403);

        return response()->json([
            'project' => $project->load('hackatimeProjects'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        abort_unless($project->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'hackatime_project_ids' => 'array',
            'hackatime_project_ids.*' => 'integer|exists:hackatime_projects,id',
        ]);

        $hackatimeProjectIds = collect($validated['hackatime_project_ids'] ?? [])->values();
        unset($validated['hackatime_project_ids']);

        $authorizedCount = HackatimeProject::where('user_id', Auth::id())
            ->whereIn('id', $hackatimeProjectIds)
            ->count();

        abort_unless($authorizedCount === $hackatimeProjectIds->count(), 403);

        $project->update($validated);
        $project->hackatimeProjects()->sync($hackatimeProjectIds->all());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Project updated successfully!',
                'project' => $project->fresh()->load('hackatimeProjects'),
            ]);
        }

        return redirect()->route('projects.index')->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        abort_unless($project->user_id === Auth::id(), 403);

        $project->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Project deleted successfully!',
            ]);
        }

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }
}
