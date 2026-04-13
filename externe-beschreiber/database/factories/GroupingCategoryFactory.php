<?php
namespace Database\Factories;
use App\Models\GroupingCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupingCategoryFactory extends Factory
{
    protected $model = GroupingCategory::class;
    public function definition(): array
    {
        return ['name_de' => fake()->word(), 'name_en' => fake()->word(), 'sale_id' => null];
    }
}
