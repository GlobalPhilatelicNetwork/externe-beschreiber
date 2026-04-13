<?php

namespace Database\Factories;

use App\Models\Consignment;
use App\Models\Lot;
use Illuminate\Database\Eloquent\Factories\Factory;

class LotFactory extends Factory
{
    protected $model = Lot::class;

    public function definition(): array
    {
        return [
            'consignment_id' => Consignment::factory(),
            'sequence_number' => fake()->unique()->numberBetween(1, 99999),
            'lot_type' => 'single',
            'grouping_category_id' => null,
            'description' => fake()->sentence(),
            'provenance' => null,
            'epos' => null,
            'starting_price' => fake()->randomFloat(2, 1, 1000),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
