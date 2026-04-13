<?php
namespace Tests\Feature\Describer;

use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Condition;
use App\Models\Consignment;
use App\Models\Destination;
use App\Models\Lot;
use App\Models\PackType;
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
            'lot_type' => 'single',
            'category_ids' => [Category::factory()->create()->id],
            'condition_ids' => [Condition::factory()->create()->id],
            'description' => 'Dt. Reich Mi.Nr. 1-3 gestempelt',
            'starting_price' => 150.00,
            'catalog_entries' => [
                ['catalog_type_id' => CatalogType::factory()->create()->id, 'catalog_number' => '1-3'],
            ],
        ];
    }

    public function test_describer_can_create_lot(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id, 'start_number' => 1, 'next_number' => 1]);

        $response = $this->actingAs($user)->post("/consignments/{$consignment->id}/lots", $this->createLotData());

        $response->assertRedirect();
        $this->assertDatabaseHas('lots', ['consignment_id' => $consignment->id, 'sequence_number' => 1, 'lot_type' => 'single']);
        $this->assertEquals(2, $consignment->fresh()->next_number);
        $lot = Lot::first();
        $this->assertCount(1, $lot->categories);
        $this->assertCount(1, $lot->conditions);
        $this->assertCount(1, $lot->catalogEntries);
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
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();
        $lot->categories()->attach($category);
        $lot->conditions()->attach($condition);

        $newData = $this->createLotData();
        $newData['description'] = 'Updated description';

        $response = $this->actingAs($user)->put("/consignments/{$consignment->id}/lots/{$lot->id}", $newData);

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

    public function test_lot_with_packages_and_destinations(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id, 'next_number' => 1]);
        $packType = PackType::factory()->create();
        $destination = Destination::factory()->create();

        $data = array_merge($this->createLotData(), [
            'destination_ids' => [$destination->id],
            'provenance' => 'Sammlung Mueller',
            'epos' => 'E-123',
            'packages' => [
                ['pack_type_id' => $packType->id, 'pack_number' => '14', 'pack_note' => 'Oben links'],
            ],
        ]);

        $response = $this->actingAs($user)->post("/consignments/{$consignment->id}/lots", $data);
        $response->assertRedirect();

        $lot = Lot::first();
        $this->assertCount(1, $lot->destinations);
        $this->assertCount(1, $lot->packages);
        $this->assertEquals('Sammlung Mueller', $lot->provenance);
        $this->assertEquals('E-123', $lot->epos);
    }
}
