<?php
namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Condition;
use App\Models\Consignment;
use App\Models\Destination;
use App\Models\Lot;
use App\Models\LotCatalogEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LotApiTest extends TestCase
{
    use RefreshDatabase;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.api.key' => 'test-api-key']);
        $this->headers = ['X-API-Key' => 'test-api-key'];
    }

    public function test_list_lots_for_consignment(): void
    {
        $consignment = Consignment::factory()->create();
        $lot = Lot::factory()->create(['consignment_id' => $consignment->id]);
        $lot->categories()->attach(Category::factory()->create());
        $lot->conditions()->attach(Condition::factory()->create());

        $response = $this->getJson("/api/v1/consignments/{$consignment->id}/lots", $this->headers);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonStructure(['data' => [['id', 'lot_type', 'categories', 'conditions', 'destinations', 'catalog_entries', 'packages']]]);
    }

    public function test_filter_lots_by_consignor_number(): void
    {
        $c1 = Consignment::factory()->create(['consignor_number' => '1111111']);
        $c2 = Consignment::factory()->create(['consignor_number' => '2222222']);
        Lot::factory()->count(3)->create(['consignment_id' => $c1->id]);
        Lot::factory()->count(2)->create(['consignment_id' => $c2->id]);

        $response = $this->getJson('/api/v1/lots?consignor_number=1111111', $this->headers);

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_lot_response_includes_all_relations(): void
    {
        $consignment = Consignment::factory()->create();
        $lot = Lot::factory()->create([
            'consignment_id' => $consignment->id,
            'provenance' => 'Test provenance',
            'epos' => 'E-999',
        ]);
        $lot->categories()->attach(Category::factory()->create());
        $lot->conditions()->attach(Condition::factory()->create());
        $lot->destinations()->attach(Destination::factory()->create());
        LotCatalogEntry::factory()->create(['lot_id' => $lot->id]);

        $response = $this->getJson("/api/v1/consignments/{$consignment->id}/lots", $this->headers);

        $data = $response->json('data.0');
        $this->assertEquals('Test provenance', $data['provenance']);
        $this->assertEquals('E-999', $data['epos']);
        $this->assertCount(1, $data['categories']);
        $this->assertCount(1, $data['conditions']);
        $this->assertCount(1, $data['destinations']);
        $this->assertCount(1, $data['catalog_entries']);
    }
}
