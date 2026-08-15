<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HackatimeSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_requires_connected_hackatime_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/hackatime-projects/sync');

        $response->assertRedirect('/projects');
        $response->assertSessionHas('error', 'Connect your Hackatime account before syncing projects.');
    }

    public function test_sync_imports_projects_from_hackatime_api(): void
    {
        $user = User::factory()->create([
            'hackatime_access_token' => 'test-token',
        ]);

        Http::fake([
            'https://hackatime.hackclub.com/api/v1/authenticated/projects*' => Http::response([
                'projects' => [
                    ['id' => 'alpha-id', 'name' => 'Alpha'],
                    ['id' => 'beta-id', 'name' => 'Beta', 'url' => 'https://hackatime.example/beta'],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->post('/hackatime-projects/sync');

        $response->assertRedirect('/projects');

        $this->assertDatabaseHas('hackatime_projects', [
            'user_id' => $user->id,
            'name' => 'Alpha',
            'external_id' => 'alpha-id',
        ]);

        $this->assertDatabaseHas('hackatime_projects', [
            'user_id' => $user->id,
            'name' => 'Beta',
            'external_id' => 'beta-id',
            'url' => 'https://hackatime.example/beta',
        ]);
    }

    public function test_sync_refreshes_token_after_unauthorized_response(): void
    {
        $user = User::factory()->create([
            'hackatime_access_token' => 'expired-token',
            'hackatime_refresh_token' => 'refresh-token',
        ]);

        config()->set('services.hackatime.client_id', 'app-client-id');
        config()->set('services.hackatime.client_secret', 'app-client-secret');
        config()->set('services.hackatime.token_url', 'https://hackatime.hackclub.com/oauth/token');

        Http::fake([
            'https://hackatime.hackclub.com/api/v1/authenticated/projects*' => Http::sequence()
                ->push(['message' => 'Unauthorized'], 401)
                ->push(['projects' => [['id' => 'gamma-id', 'name' => 'Gamma']]], 200),
            'https://hackatime.hackclub.com/oauth/token' => Http::response([
                'access_token' => 'new-token',
                'refresh_token' => 'new-refresh-token',
                'expires_in' => 3600,
            ], 200),
        ]);

        $response = $this->actingAs($user)->post('/hackatime-projects/sync');

        $response->assertRedirect('/projects');

        $user->refresh();

        $this->assertSame('new-token', $user->hackatime_access_token);
        $this->assertSame('new-refresh-token', $user->hackatime_refresh_token);

        $this->assertDatabaseHas('hackatime_projects', [
            'user_id' => $user->id,
            'name' => 'Gamma',
            'external_id' => 'gamma-id',
        ]);
    }
}
