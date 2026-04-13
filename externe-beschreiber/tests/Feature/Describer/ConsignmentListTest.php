<?php
namespace Tests\Feature\Describer;

use App\Models\Consignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsignmentListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_describer_sees_only_own_consignments(): void
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $own = Consignment::factory()->create(['user_id' => $user1->id, 'consignor_number' => '1111111']);
        $other = Consignment::factory()->create(['user_id' => $user2->id, 'consignor_number' => '2222222']);

        $response = $this->actingAs($user1)->get('/consignments');

        $response->assertStatus(200);
        $response->assertSee('1111111');
        $response->assertDontSee('2222222');
    }

    public function test_describer_can_view_own_consignment_detail(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/consignments/{$consignment->id}");
        $response->assertStatus(200);
    }

    public function test_describer_cannot_view_other_users_consignment(): void
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->get("/consignments/{$consignment->id}");
        $response->assertStatus(403);
    }

    public function test_admin_can_view_any_consignment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)->get("/consignments/{$consignment->id}");
        $response->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/consignments');
        $response->assertRedirect('/login');
    }
}
