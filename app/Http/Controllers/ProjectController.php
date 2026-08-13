<?php

namespace App\Http\Controllers;

use App\Models\Project;
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
        return Inertia::render('Projects/Index', [
            'projects' => Project::where('user_id', Auth::id())->get(),
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
        ]);

        // Add the authenticated user's ID
        $validated['user_id'] = Auth::id();

        Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'Project added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        abort_unless($project->user_id === Auth::id(), 403);

        return response()->json([
            'project' => $project,
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
        ]);

        $project->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Project updated successfully!',
                'project' => $project->fresh(),
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
