<?php
namespace Database\Factories;
use App\Models\PackType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackTypeFactory extends Factory
{
    protected $model = PackType::class;
    public function definition(): array
    {
        return [
            'name_de' => fake()->randomElement(['Karton', 'Umschlag', 'Tüte']),
            'name_en' => fake()->randomElement(['Box', 'Envelope', 'Bag']),
        ];
    }
}
