<?php

namespace Database\Factories;

use App\Models\CatalogPart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatalogPart>
 */
class CatalogPartFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name_de' => 'Hauptkatalog',
            'name_en' => 'Main Catalog',
            'is_default' => true,
        ];
    }
}
