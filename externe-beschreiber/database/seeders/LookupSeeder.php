<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\CatalogType;
use App\Models\CatalogPart;
use Illuminate\Database\Seeder;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name_de' => 'Briefmarken', 'name_en' => 'Stamps'],
            ['name_de' => 'Briefe', 'name_en' => 'Covers'],
            ['name_de' => 'Briefstücke', 'name_en' => 'Pieces'],
            ['name_de' => 'Ganzsachen', 'name_en' => 'Postal Stationery'],
            ['name_de' => 'Sammlungen', 'name_en' => 'Collections'],
            ['name_de' => 'Lots', 'name_en' => 'Lots'],
        ];
        foreach ($categories as $cat) { Category::firstOrCreate($cat); }

        $catalogTypes = [
            ['name_de' => 'Michel', 'name_en' => 'Michel'],
            ['name_de' => 'Scott', 'name_en' => 'Scott'],
            ['name_de' => 'Yvert', 'name_en' => 'Yvert'],
            ['name_de' => 'Stanley Gibbons', 'name_en' => 'Stanley Gibbons'],
            ['name_de' => 'Zumstein', 'name_en' => 'Zumstein'],
        ];
        foreach ($catalogTypes as $ct) { CatalogType::firstOrCreate($ct); }

        $catalogParts = [
            ['name_de' => 'Hauptkatalog', 'name_en' => 'Main Catalog', 'is_default' => true],
            ['name_de' => 'Nebenkatalog', 'name_en' => 'Secondary Catalog', 'is_default' => false],
        ];
        foreach ($catalogParts as $cp) {
            CatalogPart::firstOrCreate(['name_de' => $cp['name_de']], $cp);
        }
    }
}
