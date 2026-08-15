<?php

namespace App\Http\Controllers;

use App\Models\HackatimeProject;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectHackatimeLinkController extends Controller
{
    public function sync(Request $request, Project $project)
    {
        abort_unless($project->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'hackatime_project_ids' => 'array',
            'hackatime_project_ids.*' => 'integer|exists:hackatime_projects,id',
        ]);

        $ids = collect($validated['hackatime_project_ids'] ?? [])->values();

        $authorizedCount = HackatimeProject::where('user_id', Auth::id())
            ->whereIn('id', $ids)
            ->count();

        abort_unless($authorizedCount === $ids->count(), 403);

        $project->hackatimeProjects()->sync($ids->all());

        return response()->json([
            'message' => 'Hackatime project links updated successfully.',
            'project' => $project->fresh()->load('hackatimeProjects'),
        ]);
    }

    public function attach(Project $project, HackatimeProject $hackatimeProject)
    {
        abort_unless($project->user_id === Auth::id(), 403);
        abort_unless($hackatimeProject->user_id === Auth::id(), 403);

        $project->hackatimeProjects()->syncWithoutDetaching([$hackatimeProject->id]);

        return response()->json([
            'message' => 'Hackatime project linked successfully.',
            'project' => $project->fresh()->load('hackatimeProjects'),
        ]);
    }

    public function detach(Project $project, HackatimeProject $hackatimeProject)
    {
        abort_unless($project->user_id === Auth::id(), 403);
        abort_unless($hackatimeProject->user_id === Auth::id(), 403);

        $project->hackatimeProjects()->detach($hackatimeProject->id);

        return response()->json([
            'message' => 'Hackatime project unlinked successfully.',
            'project' => $project->fresh()->load('hackatimeProjects'),
        ]);
    }
}
