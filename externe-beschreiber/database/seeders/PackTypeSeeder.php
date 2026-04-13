<?php
namespace Database\Seeders;

use App\Models\PackType;
use Illuminate\Database\Seeder;

class PackTypeSeeder extends Seeder
{
    public function run(): void
    {
        $packTypes = [
            ['name_de' => 'Karton', 'name_en' => 'Box'],
            ['name_de' => 'Umschlag', 'name_en' => 'Envelope'],
            ['name_de' => 'Tüte', 'name_en' => 'Bag'],
            ['name_de' => 'Mappe', 'name_en' => 'Folder'],
        ];
        foreach ($packTypes as $pt) {
            PackType::firstOrCreate(['name_de' => $pt['name_de']], $pt);
        }
    }
}
