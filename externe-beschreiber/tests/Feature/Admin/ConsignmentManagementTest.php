<?php
namespace Tests\Feature\Admin;

use App\Models\CatalogPart;
use App\Models\Consignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsignmentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_admin_sees_all_consignments(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Consignment::factory()->count(3)->create();
        $response = $this->actingAs($admin)->get('/admin/consignments');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_consignment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $describer = User::factory()->create(['role' => 'user']);
        $catalogPart = CatalogPart::factory()->create(['is_default' => true]);

        $response = $this->actingAs($admin)->post('/admin/consignments', [
            'consignor_number' => '7389123', 'internal_nid' => '4521', 'start_number' => 1,
            'catalog_part_id' => $catalogPart->id, 'user_id' => $describer->id,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('consignments', [
            'consignor_number' => '7389123', 'internal_nid' => '4521',
            'start_number' => 1, 'next_number' => 1, 'user_id' => $describer->id, 'status' => 'open',
        ]);
    }

    public function test_admin_can_close_consignment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $consignment = Consignment::factory()->create(['status' => 'open']);
        $response = $this->actingAs($admin)->post("/admin/consignments/{$consignment->id}/close");
        $response->assertRedirect();
        $this->assertEquals('closed', $consignment->fresh()->status);
    }

    public function test_admin_can_filter_by_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Consignment::factory()->create(['status' => 'open', 'consignor_number' => '1111111']);
        Consignment::factory()->create(['status' => 'closed', 'consignor_number' => '2222222']);
        $response = $this->actingAs($admin)->get('/admin/consignments?status=open');
        $response->assertStatus(200);
        $response->assertSee('1111111');
        $response->assertDontSee('2222222');
    }

    public function test_admin_can_filter_by_describer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        Consignment::factory()->create(['user_id' => $user1->id, 'consignor_number' => '1111111']);
        Consignment::factory()->create(['user_id' => $user2->id, 'consignor_number' => '2222222']);
        $response = $this->actingAs($admin)->get("/admin/consignments?user_id={$user1->id}");
        $response->assertStatus(200);
        $response->assertSee('1111111');
        $response->assertDontSee('2222222');
    }

    public function test_describer_cannot_access_admin_consignments(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->get('/admin/consignments');
        $response->assertStatus(403);
    }
}
