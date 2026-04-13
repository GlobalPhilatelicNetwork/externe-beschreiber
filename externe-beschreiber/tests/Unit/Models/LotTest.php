<?php

namespace Tests\Unit\Models;

use App\Models\Lot;
use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Condition;
use App\Models\Consignment;
use App\Models\Destination;
use App\Models\GroupingCategory;
use App\Models\LotCatalogEntry;
use App\Models\LotPackage;
use App\Models\PackType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LotTest extends TestCase
{
    use RefreshDatabase;

    public function test_lot_belongs_to_consignment(): void
    {
        $lot = Lot::factory()->create();
        $this->assertInstanceOf(Consignment::class, $lot->consignment);
    }

    public function test_lot_has_many_categories(): void
    {
        $lot = Lot::factory()->create();
        $categories = Category::factory()->count(2)->create();
        $lot->categories()->attach($categories);
        $this->assertCount(2, $lot->fresh()->categories);
    }

    public function test_lot_has_many_conditions(): void
    {
        $lot = Lot::factory()->create();
        $conditions = Condition::factory()->count(3)->create();
        $lot->conditions()->attach($conditions);
        $this->assertCount(3, $lot->fresh()->conditions);
    }

    public function test_lot_has_many_destinations(): void
    {
        $lot = Lot::factory()->create();
        $destinations = Destination::factory()->count(2)->create();
        $lot->destinations()->attach($destinations);
        $this->assertCount(2, $lot->fresh()->destinations);
    }

    public function test_lot_belongs_to_grouping_category(): void
    {
        $gc = GroupingCategory::factory()->create();
        $lot = Lot::factory()->create(['grouping_category_id' => $gc->id]);
        $this->assertInstanceOf(GroupingCategory::class, $lot->groupingCategory);
    }

    public function test_lot_has_many_catalog_entries(): void
    {
        $lot = Lot::factory()->create();
        LotCatalogEntry::factory()->count(2)->create(['lot_id' => $lot->id]);
        $this->assertCount(2, $lot->fresh()->catalogEntries);
    }

    public function test_lot_has_many_packages(): void
    {
        $lot = Lot::factory()->create();
        LotPackage::factory()->count(2)->create(['lot_id' => $lot->id]);
        $this->assertCount(2, $lot->fresh()->packages);
    }

    public function test_lot_type_defaults_to_single(): void
    {
        $lot = Lot::factory()->create();
        $this->assertEquals('single', $lot->lot_type);
    }
}
