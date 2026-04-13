<?php
namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.api.key' => 'test-api-key']);
        $this->headers = ['X-API-Key' => 'test-api-key'];
    }

    public function test_create_user(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => 'API User', 'email' => 'api@example.com', 'password' => 'securepass123', 'role' => 'user',
        ], $this->headers);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'api@example.com']);
    }

    public function test_update_user(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        $response = $this->putJson("/api/v1/users/{$user->id}", ['name' => 'New Name'], $this->headers);
        $response->assertStatus(200);
        $this->assertEquals('New Name', $user->fresh()->name);
    }
}
