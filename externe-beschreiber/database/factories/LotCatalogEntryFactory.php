<?php
namespace Database\Factories;
use App\Models\CatalogType;
use App\Models\Lot;
use App\Models\LotCatalogEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class LotCatalogEntryFactory extends Factory
{
    protected $model = LotCatalogEntry::class;
    public function definition(): array
    {
        return [
            'lot_id' => Lot::factory(),
            'catalog_type_id' => CatalogType::factory(),
            'catalog_number' => fake()->bothify('??-####'),
        ];
    }
}
