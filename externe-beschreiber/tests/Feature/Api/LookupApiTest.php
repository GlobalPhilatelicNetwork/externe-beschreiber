<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Condition;
use App\Models\Destination;
use App\Models\GroupingCategory;
use App\Models\PackType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LookupApiTest extends TestCase
{
    use RefreshDatabase;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.api.key' => 'test-api-key']);
        $this->headers = ['X-API-Key' => 'test-api-key'];
    }

    public function test_list_categories(): void
    {
        Category::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/categories', $this->headers);
        $response->assertStatus(200)->assertJsonCount(3, 'data');
    }

    public function test_create_category(): void
    {
        $response = $this->postJson('/api/v1/categories', [
            'name_de' => 'Briefmarken', 'name_en' => 'Stamps',
        ], $this->headers);
        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', ['name_de' => 'Briefmarken']);
    }

    public function test_update_category(): void
    {
        $cat = Category::factory()->create();
        $response = $this->putJson("/api/v1/categories/{$cat->id}", [
            'name_de' => 'Neu', 'name_en' => 'New',
        ], $this->headers);
        $response->assertStatus(200);
        $this->assertEquals('Neu', $cat->fresh()->name_de);
    }

    public function test_delete_category(): void
    {
        $cat = Category::factory()->create();
        $response = $this->deleteJson("/api/v1/categories/{$cat->id}", [], $this->headers);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }

    public function test_list_grouping_categories(): void
    {
        GroupingCategory::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/grouping-categories', $this->headers);
        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_create_grouping_category(): void
    {
        $response = $this->postJson('/api/v1/grouping-categories', [
            'name_de' => 'Altdeutschland', 'name_en' => 'Old Germany',
        ], $this->headers);
        $response->assertStatus(201);
    }

    public function test_filter_grouping_categories_by_sale_id(): void
    {
        GroupingCategory::factory()->create(['sale_id' => 'SALE1']);
        GroupingCategory::factory()->create(['sale_id' => 'SALE2']);
        GroupingCategory::factory()->create(['sale_id' => 'SALE1']);

        $response = $this->getJson('/api/v1/grouping-categories?sale_id=SALE1', $this->headers);
        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_list_destinations(): void
    {
        Destination::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/destinations', $this->headers);
        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_create_destination(): void
    {
        $response = $this->postJson('/api/v1/destinations', [
            'name_de' => 'Deutschland', 'name_en' => 'Germany',
        ], $this->headers);
        $response->assertStatus(201);
    }

    public function test_list_catalog_types(): void
    {
        CatalogType::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/catalog-types', $this->headers);
        $response->assertStatus(200)->assertJsonCount(3, 'data');
    }

    public function test_create_catalog_type(): void
    {
        $response = $this->postJson('/api/v1/catalog-types', [
            'name_de' => 'Michel', 'name_en' => 'Michel',
        ], $this->headers);
        $response->assertStatus(201);
    }

    public function test_list_conditions(): void
    {
        Condition::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/conditions', $this->headers);
        $response->assertStatus(200)->assertJsonCount(3, 'data');
    }

    public function test_list_pack_types(): void
    {
        PackType::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/pack-types', $this->headers);
        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_list_lot_types(): void
    {
        $response = $this->getJson('/api/v1/lot-types', $this->headers);
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_requires_api_key(): void
    {
        $response = $this->getJson('/api/v1/categories');
        $response->assertStatus(401);
    }
}
