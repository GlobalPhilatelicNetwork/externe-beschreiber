<?php
namespace Database\Factories;
use App\Models\Lot;
use App\Models\LotPackage;
use App\Models\PackType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LotPackageFactory extends Factory
{
    protected $model = LotPackage::class;
    public function definition(): array
    {
        return [
            'lot_id' => Lot::factory(),
            'pack_type_id' => PackType::factory(),
            'pack_number' => (string) fake()->numberBetween(1, 50),
            'pack_note' => fake()->optional()->words(3, true),
        ];
    }
}
