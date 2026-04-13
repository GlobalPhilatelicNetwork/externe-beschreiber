<?php
namespace Tests\Feature\Describer;

use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Consignment;
use App\Models\Lot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LotManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function createLotData(): array
    {
        return [
            'category_id' => Category::factory()->create()->id,
            'description' => 'Dt. Reich Mi.Nr. 1-3 gestempelt',
            'catalog_type_id' => CatalogType::factory()->create()->id,
            'catalog_number' => '1-3',
            'starting_price' => 150.00,
            'notes' => 'Gute Erhaltung',
        ];
    }

    public function test_describer_can_create_lot(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id, 'start_number' => 1, 'next_number' => 1]);

        $response = $this->actingAs($user)->post("/consignments/{$consignment->id}/lots", $this->createLotData());

        $response->assertRedirect();
        $this->assertDatabaseHas('lots', ['consignment_id' => $consignment->id, 'sequence_number' => 1, 'catalog_number' => '1-3']);
        $this->assertEquals(2, $consignment->fresh()->next_number);
    }

    public function test_sequence_number_auto_increments(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id, 'start_number' => 5, 'next_number' => 5]);

        $this->actingAs($user)->post("/consignments/{$consignment->id}/lots", $this->createLotData());
        $this->actingAs($user)->post("/consignments/{$consignment->id}/lots", $this->createLotData());

        $lots = $consignment->fresh()->lots->sortBy('sequence_number');
        $this->assertEquals(5, $lots->first()->sequence_number);
        $this->assertEquals(6, $lots->last()->sequence_number);
        $this->assertEquals(7, $consignment->fresh()->next_number);
    }

    public function test_describer_can_update_own_lot(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id]);
        $lot = Lot::factory()->create(['consignment_id' => $consignment->id]);

        $response = $this->actingAs($user)->put(
            "/consignments/{$consignment->id}/lots/{$lot->id}",
            array_merge($this->createLotData(), ['description' => 'Updated description'])
        );

        $response->assertRedirect();
        $this->assertEquals('Updated description', $lot->fresh()->description);
    }

    public function test_describer_cannot_create_lot_on_closed_consignment(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id, 'status' => 'closed']);

        $response = $this->actingAs($user)->post("/consignments/{$consignment->id}/lots", $this->createLotData());
        $response->assertStatus(403);
    }

    public function test_describer_cannot_modify_lot_on_other_users_consignment(): void
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user2->id]);
        $lot = Lot::factory()->create(['consignment_id' => $consignment->id]);

        $response = $this->actingAs($user1)->put("/consignments/{$consignment->id}/lots/{$lot->id}", $this->createLotData());
        $response->assertStatus(403);
    }

    public function test_describer_can_delete_lot(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id]);
        $lot = Lot::factory()->create(['consignment_id' => $consignment->id]);

        $response = $this->actingAs($user)->delete("/consignments/{$consignment->id}/lots/{$lot->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('lots', ['id' => $lot->id]);
    }
}
