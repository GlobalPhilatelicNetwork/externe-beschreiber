<?php

namespace Tests\Unit\Models;

use App\Models\CatalogPart;
use App\Models\Consignment;
use App\Models\Lot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_consignment_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $catalogPart = CatalogPart::factory()->create();
        $consignment = Consignment::factory()->create([
            'user_id' => $user->id,
            'catalog_part_id' => $catalogPart->id,
        ]);

        $this->assertInstanceOf(User::class, $consignment->user);
        $this->assertEquals($user->id, $consignment->user->id);
    }

    public function test_consignment_belongs_to_catalog_part(): void
    {
        $catalogPart = CatalogPart::factory()->create();
        $consignment = Consignment::factory()->create([
            'catalog_part_id' => $catalogPart->id,
        ]);

        $this->assertInstanceOf(CatalogPart::class, $consignment->catalogPart);
        $this->assertEquals($catalogPart->id, $consignment->catalogPart->id);
    }

    public function test_consignment_has_many_lots(): void
    {
        $consignment = Consignment::factory()->create();
        Lot::factory()->count(3)->create(['consignment_id' => $consignment->id]);

        $this->assertCount(3, $consignment->lots);
        $this->assertInstanceOf(Lot::class, $consignment->lots->first());
    }

    public function test_consignment_is_open_by_default(): void
    {
        $consignment = Consignment::factory()->create();

        $this->assertEquals('open', $consignment->status);
        $this->assertTrue($consignment->isOpen());
    }

    public function test_consignment_can_be_closed(): void
    {
        $consignment = Consignment::factory()->closed()->create();

        $this->assertEquals('closed', $consignment->status);
        $this->assertFalse($consignment->isOpen());
    }

    public function test_is_open_returns_correct_boolean(): void
    {
        $openConsignment = Consignment::factory()->create(['status' => 'open']);
        $closedConsignment = Consignment::factory()->create(['status' => 'closed']);

        $this->assertTrue($openConsignment->isOpen());
        $this->assertFalse($closedConsignment->isOpen());
    }
}
