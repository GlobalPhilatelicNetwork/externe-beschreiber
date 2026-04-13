<?php
namespace Database\Seeders;

use App\Models\Condition;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    public function run(): void
    {
        $conditions = [
            ['name' => '**', 'image' => null, 'circuit_id' => 'C001'],
            ['name' => '*', 'image' => null, 'circuit_id' => 'C002'],
            ['name' => 'o', 'image' => null, 'circuit_id' => 'C003'],
            ['name' => "\u{2709}", 'image' => null, 'circuit_id' => 'C004'],
            ['name' => 'GS', 'image' => null, 'circuit_id' => 'C005'],
            ['name' => 'BS', 'image' => null, 'circuit_id' => 'C006'],
            ['name' => 'FDC', 'image' => null, 'circuit_id' => 'C007'],
            ['name' => 'ETB', 'image' => null, 'circuit_id' => 'C008'],
            ['name' => 'MK', 'image' => null, 'circuit_id' => 'C009'],
            ['name' => 'Lot', 'image' => null, 'circuit_id' => 'C010'],
            ['name' => 'Slg', 'image' => null, 'circuit_id' => 'C011'],
            ['name' => 'Dok', 'image' => null, 'circuit_id' => 'C012'],
        ];
        foreach ($conditions as $condition) {
            Condition::firstOrCreate(['circuit_id' => $condition['circuit_id']], $condition);
        }
    }
}
