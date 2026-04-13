<?php

namespace Database\Factories;

use App\Models\CatalogType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatalogType>
 */
class CatalogTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name_de' => fake()->word(),
            'name_en' => fake()->word(),
        ];
    }
}
