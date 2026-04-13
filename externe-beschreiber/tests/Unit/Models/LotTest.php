<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Consignment;
use App\Models\Lot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LotTest extends TestCase
{
    use RefreshDatabase;

    public function test_lot_belongs_to_consignment(): void
    {
        $consignment = Consignment::factory()->create();
        $lot = Lot::factory()->create(['consignment_id' => $consignment->id]);

        $this->assertInstanceOf(Consignment::class, $lot->consignment);
        $this->assertEquals($consignment->id, $lot->consignment->id);
    }

    public function test_lot_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $lot = Lot::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $lot->category);
        $this->assertEquals($category->id, $lot->category->id);
    }

    public function test_lot_belongs_to_catalog_type(): void
    {
        $catalogType = CatalogType::factory()->create();
        $lot = Lot::factory()->create(['catalog_type_id' => $catalogType->id]);

        $this->assertInstanceOf(CatalogType::class, $lot->catalogType);
        $this->assertEquals($catalogType->id, $lot->catalogType->id);
    }
}
