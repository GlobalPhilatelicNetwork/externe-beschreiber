<?php
namespace Tests\Feature\Api;

use App\Models\CatalogPart;
use App\Models\Consignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsignmentApiTest extends TestCase
{
    use RefreshDatabase;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.api.key' => 'test-api-key']);
        $this->headers = ['X-API-Key' => 'test-api-key'];
    }

    public function test_list_consignments(): void
    {
        Consignment::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/consignments', $this->headers);
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_filter_consignments_by_status(): void
    {
        Consignment::factory()->create(['status' => 'open']);
        Consignment::factory()->create(['status' => 'closed']);
        $response = $this->getJson('/api/v1/consignments?status=open', $this->headers);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_show_consignment(): void
    {
        $consignment = Consignment::factory()->create(['consignor_number' => '7389123']);
        $response = $this->getJson("/api/v1/consignments/{$consignment->id}", $this->headers);
        $response->assertStatus(200);
        $response->assertJsonPath('data.consignor_number', '7389123');
    }

    public function test_create_consignment(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $catalogPart = CatalogPart::factory()->create();
        $response = $this->postJson('/api/v1/consignments', [
            'consignor_number' => '7389999', 'internal_nid' => '9999', 'start_number' => 1,
            'catalog_part_id' => $catalogPart->id, 'user_id' => $user->id, 'sale_id' => 'SALE-X',
        ], $this->headers);
        $response->assertStatus(201);
        $response->assertJsonPath('data.consignor_number', '7389999');
        $response->assertJsonPath('data.sale_id', 'SALE-X');
    }

    public function test_update_consignment(): void
    {
        $consignment = Consignment::factory()->create();
        $response = $this->putJson("/api/v1/consignments/{$consignment->id}", [
            'consignor_number' => '0000000',
        ], $this->headers);
        $response->assertStatus(200);
        $this->assertEquals('0000000', $consignment->fresh()->consignor_number);
    }

    public function test_update_consignment_sale_id(): void
    {
        $consignment = Consignment::factory()->create();
        $response = $this->putJson("/api/v1/consignments/{$consignment->id}", [
            'sale_id' => 'SALE-UPDATED',
        ], $this->headers);
        $response->assertStatus(200);
        $this->assertEquals('SALE-UPDATED', $consignment->fresh()->sale_id);
    }

    public function test_requires_api_key(): void
    {
        $response = $this->getJson('/api/v1/consignments');
        $response->assertStatus(401);
    }
}
