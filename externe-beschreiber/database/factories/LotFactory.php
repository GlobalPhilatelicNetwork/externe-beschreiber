<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Consignment;
use App\Models\Lot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lot>
 */
class LotFactory extends Factory
{
    public function definition(): array
    {
        return [
            'consignment_id' => Consignment::factory(),
            'sequence_number' => fake()->unique()->numberBetween(1, 99999),
            'category_id' => Category::factory(),
            'description' => fake()->sentence(),
            'catalog_type_id' => CatalogType::factory(),
            'catalog_number' => fake()->bothify('??-####'),
            'starting_price' => fake()->randomFloat(2, 1, 1000),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
