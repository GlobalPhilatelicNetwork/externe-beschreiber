<?php
namespace Tests\Feature\Admin;

use App\Models\User;
use App\Mail\CredentialsMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_admin_can_see_user_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(3)->create(['role' => 'user']);
        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_describer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Hans Schmidt', 'email' => 'hans@example.com', 'password' => 'securepass123', 'role' => 'user',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'hans@example.com', 'role' => 'user']);
    }

    public function test_admin_can_create_and_send_credentials(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Hans Schmidt', 'email' => 'hans@example.com', 'password' => 'securepass123', 'role' => 'user', 'send_credentials' => '1',
        ]);
        $response->assertRedirect();
        Mail::assertSent(CredentialsMail::class, fn($mail) => $mail->hasTo('hans@example.com'));
    }

    public function test_admin_can_resend_credentials(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($admin)->post("/admin/users/{$user->id}/send-credentials");
        $response->assertRedirect();
        Mail::assertSent(CredentialsMail::class);
    }

    public function test_admin_can_update_describer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user', 'name' => 'Old Name']);
        $response = $this->actingAs($admin)->put("/admin/users/{$user->id}", [
            'name' => 'New Name', 'email' => $user->email, 'role' => 'user',
        ]);
        $response->assertRedirect();
        $this->assertEquals('New Name', $user->fresh()->name);
    }

    public function test_describer_cannot_access_user_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertStatus(403);
    }
}
