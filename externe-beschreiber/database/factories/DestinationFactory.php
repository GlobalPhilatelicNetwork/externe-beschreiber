<?php
namespace Database\Factories;
use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

class DestinationFactory extends Factory
{
    protected $model = Destination::class;
    public function definition(): array
    {
        return ['name_de' => fake()->country(), 'name_en' => fake()->country()];
    }
}
