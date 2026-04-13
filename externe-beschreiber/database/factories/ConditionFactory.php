<?php
namespace Database\Factories;
use App\Models\Condition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConditionFactory extends Factory
{
    protected $model = Condition::class;
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['**', '*', 'o', 'BS', 'FDC']),
            'image' => null,
            'circuit_id' => 'C' . fake()->numerify('###'),
        ];
    }
}
