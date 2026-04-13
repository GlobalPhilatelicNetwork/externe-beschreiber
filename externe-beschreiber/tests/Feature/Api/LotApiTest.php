<?php
namespace Tests\Feature\Api;

use App\Models\Consignment;
use App\Models\Lot;
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
        Lot::factory()->count(5)->create(['consignment_id' => $consignment->id]);
        $response = $this->getJson("/api/v1/consignments/{$consignment->id}/lots", $this->headers);
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
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
}
