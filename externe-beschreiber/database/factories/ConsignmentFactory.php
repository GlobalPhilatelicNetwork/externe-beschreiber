<?php

namespace Database\Factories;

use App\Models\CatalogPart;
use App\Models\Consignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Consignment>
 */
class ConsignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'consignor_number' => fake()->numerify('C-####'),
            'internal_nid' => fake()->numerify('NID-####'),
            'sale_id' => null,
            'start_number' => 1,
            'next_number' => 1,
            'catalog_part_id' => CatalogPart::factory(),
            'user_id' => User::factory(),
            'status' => 'open',
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }
}
