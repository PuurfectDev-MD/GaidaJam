<?php

namespace Tests\Feature;

use App\Models\HackatimeProject;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectHackatimeLinkingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_project_with_multiple_hackatime_links(): void
    {
        $user = User::factory()->create();

        $hackatimeProjectA = HackatimeProject::create([
            'user_id' => $user->id,
            'name' => 'Hackatime A',
            'external_id' => 'a-1',
        ]);

        $hackatimeProjectB = HackatimeProject::create([
            'user_id' => $user->id,
            'name' => 'Hackatime B',
            'external_id' => 'b-1',
        ]);

        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'My App Project',
            'description' => 'Project description',
            'url' => 'https://example.com',
            'hackatime_project_ids' => [$hackatimeProjectA->id, $hackatimeProjectB->id],
        ]);

        $response->assertRedirect('/projects');

        $project = Project::where('name', 'My App Project')->firstOrFail();

        $this->assertDatabaseHas('hackatime_project_project', [
            'project_id' => $project->id,
            'hackatime_project_id' => $hackatimeProjectA->id,
        ]);

        $this->assertDatabaseHas('hackatime_project_project', [
            'project_id' => $project->id,
            'hackatime_project_id' => $hackatimeProjectB->id,
        ]);
    }

    public function test_user_cannot_link_another_users_hackatime_project_on_create(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $foreignHackatimeProject = HackatimeProject::create([
            'user_id' => $otherUser->id,
            'name' => 'Foreign Project',
            'external_id' => 'foreign-1',
        ]);

        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'Unauthorized Link Attempt',
            'hackatime_project_ids' => [$foreignHackatimeProject->id],
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_sync_hackatime_links_for_existing_project(): void
    {
        $user = User::factory()->create();

        $project = Project::create([
            'user_id' => $user->id,
            'name' => 'Sync Target',
        ]);

        $hackatimeProjectA = HackatimeProject::create([
            'user_id' => $user->id,
            'name' => 'Sync A',
            'external_id' => 'sync-a',
        ]);

        $hackatimeProjectB = HackatimeProject::create([
            'user_id' => $user->id,
            'name' => 'Sync B',
            'external_id' => 'sync-b',
        ]);

        $response = $this->actingAs($user)->put("/projects/{$project->id}/hackatime-projects", [
            'hackatime_project_ids' => [$hackatimeProjectA->id, $hackatimeProjectB->id],
        ]);

        $response->assertOk();

        $this->assertDatabaseCount('hackatime_project_project', 2);
    }
}
