# Erweiterte Losfelder Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Extend lot descriptions with multi-select categories, conditions, destinations, multiple catalog entries, packaging, groupingcategory, provenance, epos, lot type, and add Lookup CRUD API endpoints.

**Architecture:** Extend existing Laravel models with pivot tables for many-to-many (categories, conditions, destinations), child tables for 1:N (catalog entries, packages), new lookup tables with API CRUD, and a completely reworked Livewire form with HTML editor. Existing tests must be adapted for the changed data model.

**Tech Stack:** Laravel 12, Livewire 3, TailwindCSS v4, Trix editor (for HTML editing), PHPUnit, SQLite in-memory for tests

---

## File Structure

```
externe-beschreiber/
├── database/
│   ├── migrations/
│   │   ├── xxxx_create_conditions_table.php              (create)
│   │   ├── xxxx_create_grouping_categories_table.php     (create)
│   │   ├── xxxx_create_destinations_table.php            (create)
│   │   ├── xxxx_create_pack_types_table.php              (create)
│   │   ├── xxxx_modify_lots_table_add_new_fields.php     (create)
│   │   ├── xxxx_create_lot_category_table.php            (create)
│   │   ├── xxxx_create_lot_condition_table.php           (create)
│   │   ├── xxxx_create_lot_destination_table.php         (create)
│   │   ├── xxxx_create_lot_catalog_entries_table.php     (create)
│   │   └── xxxx_create_lot_packages_table.php            (create)
│   ├── factories/
│   │   ├── ConditionFactory.php                          (create)
│   │   ├── GroupingCategoryFactory.php                   (create)
│   │   ├── DestinationFactory.php                        (create)
│   │   ├── PackTypeFactory.php                           (create)
│   │   ├── LotCatalogEntryFactory.php                    (create)
│   │   ├── LotPackageFactory.php                         (create)
│   │   └── LotFactory.php                                (modify)
│   └── seeders/
│       ├── ConditionSeeder.php                           (create)
│       ├── PackTypeSeeder.php                            (create)
│       └── DatabaseSeeder.php                            (modify)
├── app/
│   ├── Models/
│   │   ├── Condition.php                                 (create)
│   │   ├── GroupingCategory.php                          (create)
│   │   ├── Destination.php                               (create)
│   │   ├── PackType.php                                  (create)
│   │   ├── LotCatalogEntry.php                           (create)
│   │   ├── LotPackage.php                                (create)
│   │   └── Lot.php                                       (modify)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── LookupController.php                  (create)
│   │   │   │   └── LotController.php                     (modify)
│   │   │   └── Describer/
│   │   │       └── LotController.php                     (modify)
│   │   └── Requests/
│   │       ├── StoreLotRequest.php                       (modify)
│   │       └── UpdateLotRequest.php                      (modify)
│   └── Livewire/
│       └── LotForm.php                                   (modify — full rewrite)
├── resources/
│   ├── views/
│   │   ├── livewire/
│   │   │   └── lot-form.blade.php                        (modify — full rewrite)
│   │   └── describer/
│   │       ├── consignments/
│   │       │   └── show.blade.php                        (modify)
│   │       └── lots/
│   │           └── edit.blade.php                        (modify — full rewrite)
│   └── lang/
│       ├── de/messages.php                               (modify)
│       └── en/messages.php                               (modify)
├── routes/
│   └── api.php                                           (modify)
└── tests/
    ├── Feature/
    │   ├── Api/
    │   │   ├── LookupApiTest.php                         (create)
    │   │   ├── LotApiTest.php                            (modify)
    │   │   └── ConsignmentApiTest.php                    (no change)
    │   └── Describer/
    │       └── LotManagementTest.php                     (modify)
    └── Unit/
        └── Models/
            └── LotTest.php                               (modify)
```

---

### Task 1: Neue Migrationen

**Files:**
- Create: `database/migrations/xxxx_create_conditions_table.php`
- Create: `database/migrations/xxxx_create_grouping_categories_table.php`
- Create: `database/migrations/xxxx_create_destinations_table.php`
- Create: `database/migrations/xxxx_create_pack_types_table.php`
- Create: `database/migrations/xxxx_modify_lots_table_add_new_fields.php`
- Create: `database/migrations/xxxx_create_lot_category_table.php`
- Create: `database/migrations/xxxx_create_lot_condition_table.php`
- Create: `database/migrations/xxxx_create_lot_destination_table.php`
- Create: `database/migrations/xxxx_create_lot_catalog_entries_table.php`
- Create: `database/migrations/xxxx_create_lot_packages_table.php`

- [ ] **Step 1: Create conditions table migration**

```bash
php artisan make:migration create_conditions_table
```

```php
public function up(): void
{
    Schema::create('conditions', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('image')->nullable();
        $table->string('circuit_id');
        $table->timestamps();
    });
}
```

- [ ] **Step 2: Create grouping_categories table migration**

```bash
php artisan make:migration create_grouping_categories_table
```

```php
public function up(): void
{
    Schema::create('grouping_categories', function (Blueprint $table) {
        $table->id();
        $table->string('name_de');
        $table->string('name_en');
        $table->timestamps();
    });
}
```

- [ ] **Step 3: Create destinations table migration**

```bash
php artisan make:migration create_destinations_table
```

```php
public function up(): void
{
    Schema::create('destinations', function (Blueprint $table) {
        $table->id();
        $table->string('name_de');
        $table->string('name_en');
        $table->timestamps();
    });
}
```

- [ ] **Step 4: Create pack_types table migration**

```bash
php artisan make:migration create_pack_types_table
```

```php
public function up(): void
{
    Schema::create('pack_types', function (Blueprint $table) {
        $table->id();
        $table->string('name_de');
        $table->string('name_en');
        $table->timestamps();
    });
}
```

- [ ] **Step 5: Modify lots table migration**

```bash
php artisan make:migration modify_lots_table_add_new_fields --table=lots
```

```php
public function up(): void
{
    Schema::table('lots', function (Blueprint $table) {
        $table->enum('lot_type', ['single', 'collection'])->default('single')->after('sequence_number');
        $table->foreignId('grouping_category_id')->nullable()->constrained('grouping_categories')->nullOnDelete()->after('lot_type');
        $table->text('provenance')->nullable()->after('description');
        $table->string('epos')->nullable()->after('provenance');

        $table->dropForeign(['category_id']);
        $table->dropColumn('category_id');
        $table->dropForeign(['catalog_type_id']);
        $table->dropColumn(['catalog_type_id', 'catalog_number']);
    });
}

public function down(): void
{
    Schema::table('lots', function (Blueprint $table) {
        $table->foreignId('category_id')->constrained('categories');
        $table->foreignId('catalog_type_id')->constrained('catalog_types');
        $table->string('catalog_number');

        $table->dropForeign(['grouping_category_id']);
        $table->dropColumn(['lot_type', 'grouping_category_id', 'provenance', 'epos']);
    });
}
```

- [ ] **Step 6: Create pivot tables**

```bash
php artisan make:migration create_lot_category_table
php artisan make:migration create_lot_condition_table
php artisan make:migration create_lot_destination_table
```

`create_lot_category_table`:
```php
public function up(): void
{
    Schema::create('lot_category', function (Blueprint $table) {
        $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
        $table->foreignId('category_id')->constrained();
        $table->primary(['lot_id', 'category_id']);
    });
}
```

`create_lot_condition_table`:
```php
public function up(): void
{
    Schema::create('lot_condition', function (Blueprint $table) {
        $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
        $table->foreignId('condition_id')->constrained();
        $table->primary(['lot_id', 'condition_id']);
    });
}
```

`create_lot_destination_table`:
```php
public function up(): void
{
    Schema::create('lot_destination', function (Blueprint $table) {
        $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
        $table->foreignId('destination_id')->constrained();
        $table->primary(['lot_id', 'destination_id']);
    });
}
```

- [ ] **Step 7: Create detail tables**

```bash
php artisan make:migration create_lot_catalog_entries_table
php artisan make:migration create_lot_packages_table
```

`create_lot_catalog_entries_table`:
```php
public function up(): void
{
    Schema::create('lot_catalog_entries', function (Blueprint $table) {
        $table->id();
        $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
        $table->foreignId('catalog_type_id')->constrained('catalog_types');
        $table->string('catalog_number');
        $table->timestamps();
    });
}
```

`create_lot_packages_table`:
```php
public function up(): void
{
    Schema::create('lot_packages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
        $table->foreignId('pack_type_id')->constrained('pack_types');
        $table->string('pack_number');
        $table->string('pack_note')->nullable();
        $table->timestamps();
    });
}
```

- [ ] **Step 8: Commit**

```bash
git add database/migrations/
git commit -m "feat: Migrationen fuer erweiterte Losfelder (ref #3)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 2: Neue Models & Factories

**Files:**
- Create: `app/Models/Condition.php`
- Create: `app/Models/GroupingCategory.php`
- Create: `app/Models/Destination.php`
- Create: `app/Models/PackType.php`
- Create: `app/Models/LotCatalogEntry.php`
- Create: `app/Models/LotPackage.php`
- Create: `database/factories/ConditionFactory.php`
- Create: `database/factories/GroupingCategoryFactory.php`
- Create: `database/factories/DestinationFactory.php`
- Create: `database/factories/PackTypeFactory.php`
- Create: `database/factories/LotCatalogEntryFactory.php`
- Create: `database/factories/LotPackageFactory.php`

- [ ] **Step 1: Create Condition model**

`app/Models/Condition.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'circuit_id'];

    public function getDisplayAttribute(): string
    {
        return $this->image ?: $this->name;
    }
}
```

- [ ] **Step 2: Create GroupingCategory model**

`app/Models/GroupingCategory.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupingCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name_de', 'name_en'];

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }
}
```

- [ ] **Step 3: Create Destination model**

`app/Models/Destination.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = ['name_de', 'name_en'];

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }
}
```

- [ ] **Step 4: Create PackType model**

`app/Models/PackType.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackType extends Model
{
    use HasFactory;

    protected $fillable = ['name_de', 'name_en'];

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }
}
```

- [ ] **Step 5: Create LotCatalogEntry model**

`app/Models/LotCatalogEntry.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotCatalogEntry extends Model
{
    use HasFactory;

    protected $fillable = ['lot_id', 'catalog_type_id', 'catalog_number'];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function catalogType(): BelongsTo
    {
        return $this->belongsTo(CatalogType::class);
    }
}
```

- [ ] **Step 6: Create LotPackage model**

`app/Models/LotPackage.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotPackage extends Model
{
    use HasFactory;

    protected $fillable = ['lot_id', 'pack_type_id', 'pack_number', 'pack_note'];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function packType(): BelongsTo
    {
        return $this->belongsTo(PackType::class);
    }
}
```

- [ ] **Step 7: Create all factories**

`database/factories/ConditionFactory.php`:
```php
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
```

`database/factories/GroupingCategoryFactory.php`:
```php
<?php
namespace Database\Factories;

use App\Models\GroupingCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupingCategoryFactory extends Factory
{
    protected $model = GroupingCategory::class;

    public function definition(): array
    {
        return [
            'name_de' => fake()->word(),
            'name_en' => fake()->word(),
        ];
    }
}
```

`database/factories/DestinationFactory.php`:
```php
<?php
namespace Database\Factories;

use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

class DestinationFactory extends Factory
{
    protected $model = Destination::class;

    public function definition(): array
    {
        return [
            'name_de' => fake()->country(),
            'name_en' => fake()->country(),
        ];
    }
}
```

`database/factories/PackTypeFactory.php`:
```php
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
```

`database/factories/LotCatalogEntryFactory.php`:
```php
<?php
namespace Database\Factories;

use App\Models\CatalogType;
use App\Models\Lot;
use App\Models\LotCatalogEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class LotCatalogEntryFactory extends Factory
{
    protected $model = LotCatalogEntry::class;

    public function definition(): array
    {
        return [
            'lot_id' => Lot::factory(),
            'catalog_type_id' => CatalogType::factory(),
            'catalog_number' => fake()->bothify('??-####'),
        ];
    }
}
```

`database/factories/LotPackageFactory.php`:
```php
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
```

- [ ] **Step 8: Commit**

```bash
git add app/Models/ database/factories/
git commit -m "feat: Neue Models und Factories fuer erweiterte Losfelder (ref #3)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 3: Lot Model aktualisieren & LotFactory anpassen

**Files:**
- Modify: `app/Models/Lot.php`
- Modify: `database/factories/LotFactory.php`
- Modify: `tests/Unit/Models/LotTest.php`

- [ ] **Step 1: Update Lot model tests**

Replace `tests/Unit/Models/LotTest.php`:

```php
<?php

namespace Tests\Unit\Models;

use App\Models\Lot;
use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Condition;
use App\Models\Consignment;
use App\Models\Destination;
use App\Models\GroupingCategory;
use App\Models\LotCatalogEntry;
use App\Models\LotPackage;
use App\Models\PackType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LotTest extends TestCase
{
    use RefreshDatabase;

    public function test_lot_belongs_to_consignment(): void
    {
        $lot = Lot::factory()->create();
        $this->assertInstanceOf(Consignment::class, $lot->consignment);
    }

    public function test_lot_has_many_categories(): void
    {
        $lot = Lot::factory()->create();
        $categories = Category::factory()->count(2)->create();
        $lot->categories()->attach($categories);

        $this->assertCount(2, $lot->fresh()->categories);
    }

    public function test_lot_has_many_conditions(): void
    {
        $lot = Lot::factory()->create();
        $conditions = Condition::factory()->count(3)->create();
        $lot->conditions()->attach($conditions);

        $this->assertCount(3, $lot->fresh()->conditions);
    }

    public function test_lot_has_many_destinations(): void
    {
        $lot = Lot::factory()->create();
        $destinations = Destination::factory()->count(2)->create();
        $lot->destinations()->attach($destinations);

        $this->assertCount(2, $lot->fresh()->destinations);
    }

    public function test_lot_belongs_to_grouping_category(): void
    {
        $gc = GroupingCategory::factory()->create();
        $lot = Lot::factory()->create(['grouping_category_id' => $gc->id]);

        $this->assertInstanceOf(GroupingCategory::class, $lot->groupingCategory);
    }

    public function test_lot_has_many_catalog_entries(): void
    {
        $lot = Lot::factory()->create();
        LotCatalogEntry::factory()->count(2)->create(['lot_id' => $lot->id]);

        $this->assertCount(2, $lot->fresh()->catalogEntries);
    }

    public function test_lot_has_many_packages(): void
    {
        $lot = Lot::factory()->create();
        LotPackage::factory()->count(2)->create(['lot_id' => $lot->id]);

        $this->assertCount(2, $lot->fresh()->packages);
    }

    public function test_lot_type_defaults_to_single(): void
    {
        $lot = Lot::factory()->create();
        $this->assertEquals('single', $lot->lot_type);
    }
}
```

- [ ] **Step 2: Run tests — should fail**

```bash
php artisan test tests/Unit/Models/LotTest.php
```

Expected: FAIL — Lot model still has old relationships.

- [ ] **Step 3: Update Lot model**

Replace `app/Models/Lot.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignment_id',
        'sequence_number',
        'lot_type',
        'grouping_category_id',
        'description',
        'provenance',
        'epos',
        'starting_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'starting_price' => 'decimal:2',
        ];
    }

    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }

    public function groupingCategory(): BelongsTo
    {
        return $this->belongsTo(GroupingCategory::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'lot_category');
    }

    public function conditions(): BelongsToMany
    {
        return $this->belongsToMany(Condition::class, 'lot_condition');
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class, 'lot_destination');
    }

    public function catalogEntries(): HasMany
    {
        return $this->hasMany(LotCatalogEntry::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(LotPackage::class);
    }
}
```

- [ ] **Step 4: Update LotFactory**

Replace `database/factories/LotFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Consignment;
use App\Models\Lot;
use Illuminate\Database\Eloquent\Factories\Factory;

class LotFactory extends Factory
{
    protected $model = Lot::class;

    public function definition(): array
    {
        return [
            'consignment_id' => Consignment::factory(),
            'sequence_number' => fake()->unique()->numberBetween(1, 99999),
            'lot_type' => 'single',
            'grouping_category_id' => null,
            'description' => fake()->sentence(),
            'provenance' => null,
            'epos' => null,
            'starting_price' => fake()->randomFloat(2, 1, 1000),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
```

- [ ] **Step 5: Run tests — should pass**

```bash
php artisan test tests/Unit/Models/LotTest.php
```

Expected: 8 tests PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Models/Lot.php database/factories/LotFactory.php tests/Unit/Models/LotTest.php
git commit -m "feat: Lot Model mit neuen Relationships und Factory (ref #3)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 4: Seeders

**Files:**
- Create: `database/seeders/ConditionSeeder.php`
- Create: `database/seeders/PackTypeSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Create ConditionSeeder**

`database/seeders/ConditionSeeder.php`:
```php
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
            ['name' => '✉', 'image' => null, 'circuit_id' => 'C004'],
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
```

NOTE: The circuit_ids and exact names/images will be updated by the user later. These are placeholder values that match the design mockup.

- [ ] **Step 2: Create PackTypeSeeder**

`database/seeders/PackTypeSeeder.php`:
```php
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
```

- [ ] **Step 3: Update DatabaseSeeder**

Add new seeders to `database/seeders/DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([
        LookupSeeder::class,
        ConditionSeeder::class,
        PackTypeSeeder::class,
        AdminSeeder::class,
    ]);
}
```

- [ ] **Step 4: Commit**

```bash
git add database/seeders/
git commit -m "feat: Seeders fuer Conditions und PackTypes (ref #3)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 5: Lookup API Controller & Routes

**Files:**
- Create: `app/Http/Controllers/Api/LookupController.php`
- Modify: `routes/api.php`
- Create: `tests/Feature/Api/LookupApiTest.php`

- [ ] **Step 1: Write Lookup API tests**

`tests/Feature/Api/LookupApiTest.php`:

```php
<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Condition;
use App\Models\Destination;
use App\Models\GroupingCategory;
use App\Models\PackType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LookupApiTest extends TestCase
{
    use RefreshDatabase;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.api.key' => 'test-api-key']);
        $this->headers = ['X-API-Key' => 'test-api-key'];
    }

    // Categories CRUD
    public function test_list_categories(): void
    {
        Category::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/categories', $this->headers);
        $response->assertStatus(200)->assertJsonCount(3, 'data');
    }

    public function test_create_category(): void
    {
        $response = $this->postJson('/api/v1/categories', [
            'name_de' => 'Briefmarken', 'name_en' => 'Stamps',
        ], $this->headers);
        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', ['name_de' => 'Briefmarken']);
    }

    public function test_update_category(): void
    {
        $cat = Category::factory()->create();
        $response = $this->putJson("/api/v1/categories/{$cat->id}", [
            'name_de' => 'Neu', 'name_en' => 'New',
        ], $this->headers);
        $response->assertStatus(200);
        $this->assertEquals('Neu', $cat->fresh()->name_de);
    }

    public function test_delete_category(): void
    {
        $cat = Category::factory()->create();
        $response = $this->deleteJson("/api/v1/categories/{$cat->id}", [], $this->headers);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }

    // Grouping Categories CRUD
    public function test_list_grouping_categories(): void
    {
        GroupingCategory::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/grouping-categories', $this->headers);
        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_create_grouping_category(): void
    {
        $response = $this->postJson('/api/v1/grouping-categories', [
            'name_de' => 'Altdeutschland', 'name_en' => 'Old Germany',
        ], $this->headers);
        $response->assertStatus(201);
    }

    // Destinations CRUD
    public function test_list_destinations(): void
    {
        Destination::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/destinations', $this->headers);
        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_create_destination(): void
    {
        $response = $this->postJson('/api/v1/destinations', [
            'name_de' => 'Deutschland', 'name_en' => 'Germany',
        ], $this->headers);
        $response->assertStatus(201);
    }

    // Catalog Types CRUD
    public function test_list_catalog_types(): void
    {
        CatalogType::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/catalog-types', $this->headers);
        $response->assertStatus(200)->assertJsonCount(3, 'data');
    }

    public function test_create_catalog_type(): void
    {
        $response = $this->postJson('/api/v1/catalog-types', [
            'name_de' => 'Michel', 'name_en' => 'Michel',
        ], $this->headers);
        $response->assertStatus(201);
    }

    // Read-only endpoints
    public function test_list_conditions(): void
    {
        Condition::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/conditions', $this->headers);
        $response->assertStatus(200)->assertJsonCount(3, 'data');
    }

    public function test_list_pack_types(): void
    {
        PackType::factory()->count(2)->create();
        $response = $this->getJson('/api/v1/pack-types', $this->headers);
        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_list_lot_types(): void
    {
        $response = $this->getJson('/api/v1/lot-types', $this->headers);
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_requires_api_key(): void
    {
        $response = $this->getJson('/api/v1/categories');
        $response->assertStatus(401);
    }
}
```

- [ ] **Step 2: Run tests — should fail**

```bash
php artisan test tests/Feature/Api/LookupApiTest.php
```

Expected: FAIL.

- [ ] **Step 3: Create LookupController**

`app/Http/Controllers/Api/LookupController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Condition;
use App\Models\Destination;
use App\Models\GroupingCategory;
use App\Models\PackType;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    // --- Categories ---
    public function indexCategories()
    {
        return response()->json(['data' => Category::all()]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate(['name_de' => 'required|string', 'name_en' => 'required|string']);
        $item = Category::create($validated);
        return response()->json(['data' => $item], 201);
    }

    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate(['name_de' => 'sometimes|string', 'name_en' => 'sometimes|string']);
        $category->update($validated);
        return response()->json(['data' => $category->fresh()]);
    }

    public function destroyCategory(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // --- Catalog Types ---
    public function indexCatalogTypes()
    {
        return response()->json(['data' => CatalogType::all()]);
    }

    public function storeCatalogType(Request $request)
    {
        $validated = $request->validate(['name_de' => 'required|string', 'name_en' => 'required|string']);
        $item = CatalogType::create($validated);
        return response()->json(['data' => $item], 201);
    }

    public function updateCatalogType(Request $request, CatalogType $catalogType)
    {
        $validated = $request->validate(['name_de' => 'sometimes|string', 'name_en' => 'sometimes|string']);
        $catalogType->update($validated);
        return response()->json(['data' => $catalogType->fresh()]);
    }

    public function destroyCatalogType(CatalogType $catalogType)
    {
        $catalogType->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // --- Grouping Categories ---
    public function indexGroupingCategories()
    {
        return response()->json(['data' => GroupingCategory::all()]);
    }

    public function storeGroupingCategory(Request $request)
    {
        $validated = $request->validate(['name_de' => 'required|string', 'name_en' => 'required|string']);
        $item = GroupingCategory::create($validated);
        return response()->json(['data' => $item], 201);
    }

    public function updateGroupingCategory(Request $request, GroupingCategory $groupingCategory)
    {
        $validated = $request->validate(['name_de' => 'sometimes|string', 'name_en' => 'sometimes|string']);
        $groupingCategory->update($validated);
        return response()->json(['data' => $groupingCategory->fresh()]);
    }

    public function destroyGroupingCategory(GroupingCategory $groupingCategory)
    {
        $groupingCategory->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // --- Destinations ---
    public function indexDestinations()
    {
        return response()->json(['data' => Destination::all()]);
    }

    public function storeDestination(Request $request)
    {
        $validated = $request->validate(['name_de' => 'required|string', 'name_en' => 'required|string']);
        $item = Destination::create($validated);
        return response()->json(['data' => $item], 201);
    }

    public function updateDestination(Request $request, Destination $destination)
    {
        $validated = $request->validate(['name_de' => 'sometimes|string', 'name_en' => 'sometimes|string']);
        $destination->update($validated);
        return response()->json(['data' => $destination->fresh()]);
    }

    public function destroyDestination(Destination $destination)
    {
        $destination->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // --- Read-only ---
    public function indexConditions()
    {
        return response()->json(['data' => Condition::all()]);
    }

    public function indexPackTypes()
    {
        return response()->json(['data' => PackType::all()]);
    }

    public function indexLotTypes()
    {
        return response()->json(['data' => [
            ['value' => 'single', 'label_de' => 'Einzellos', 'label_en' => 'Single Lot'],
            ['value' => 'collection', 'label_de' => 'Sammlung', 'label_en' => 'Collection'],
        ]]);
    }
}
```

- [ ] **Step 4: Add Lookup routes to api.php**

Add inside the existing `Route::middleware(ApiKeyMiddleware::class)->prefix('v1')->group()`:

```php
use App\Http\Controllers\Api\LookupController;

// Lookup CRUD
Route::get('/categories', [LookupController::class, 'indexCategories']);
Route::post('/categories', [LookupController::class, 'storeCategory']);
Route::put('/categories/{category}', [LookupController::class, 'updateCategory']);
Route::delete('/categories/{category}', [LookupController::class, 'destroyCategory']);

Route::get('/catalog-types', [LookupController::class, 'indexCatalogTypes']);
Route::post('/catalog-types', [LookupController::class, 'storeCatalogType']);
Route::put('/catalog-types/{catalogType}', [LookupController::class, 'updateCatalogType']);
Route::delete('/catalog-types/{catalogType}', [LookupController::class, 'destroyCatalogType']);

Route::get('/grouping-categories', [LookupController::class, 'indexGroupingCategories']);
Route::post('/grouping-categories', [LookupController::class, 'storeGroupingCategory']);
Route::put('/grouping-categories/{groupingCategory}', [LookupController::class, 'updateGroupingCategory']);
Route::delete('/grouping-categories/{groupingCategory}', [LookupController::class, 'destroyGroupingCategory']);

Route::get('/destinations', [LookupController::class, 'indexDestinations']);
Route::post('/destinations', [LookupController::class, 'storeDestination']);
Route::put('/destinations/{destination}', [LookupController::class, 'updateDestination']);
Route::delete('/destinations/{destination}', [LookupController::class, 'destroyDestination']);

// Read-only lookups
Route::get('/conditions', [LookupController::class, 'indexConditions']);
Route::get('/pack-types', [LookupController::class, 'indexPackTypes']);
Route::get('/lot-types', [LookupController::class, 'indexLotTypes']);
```

- [ ] **Step 5: Run tests — should pass**

```bash
php artisan test tests/Feature/Api/LookupApiTest.php
```

Expected: 14 tests PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Api/LookupController.php routes/api.php tests/Feature/Api/LookupApiTest.php
git commit -m "feat: Lookup API CRUD Endpunkte (ref #3)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 6: Validation Requests aktualisieren

**Files:**
- Modify: `app/Http/Requests/StoreLotRequest.php`
- Modify: `app/Http/Requests/UpdateLotRequest.php`

- [ ] **Step 1: Update StoreLotRequest**

Replace `app/Http/Requests/StoreLotRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageLots', $this->route('consignment'));
    }

    public function rules(): array
    {
        return [
            'lot_type' => ['required', 'in:single,collection'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['exists:categories,id'],
            'grouping_category_id' => ['nullable', 'exists:grouping_categories,id'],
            'condition_ids' => ['required', 'array', 'min:1'],
            'condition_ids.*' => ['exists:conditions,id'],
            'destination_ids' => ['nullable', 'array'],
            'destination_ids.*' => ['exists:destinations,id'],
            'description' => ['required', 'string'],
            'provenance' => ['nullable', 'string'],
            'epos' => ['nullable', 'string', 'max:255'],
            'starting_price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
            'catalog_entries' => ['nullable', 'array'],
            'catalog_entries.*.catalog_type_id' => ['required', 'exists:catalog_types,id'],
            'catalog_entries.*.catalog_number' => ['required', 'string', 'max:255'],
            'packages' => ['nullable', 'array'],
            'packages.*.pack_type_id' => ['required', 'exists:pack_types,id'],
            'packages.*.pack_number' => ['required', 'string', 'max:255'],
            'packages.*.pack_note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
```

- [ ] **Step 2: Update UpdateLotRequest**

Replace `app/Http/Requests/UpdateLotRequest.php` with identical rules:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageLots', $this->route('consignment'));
    }

    public function rules(): array
    {
        return [
            'lot_type' => ['required', 'in:single,collection'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['exists:categories,id'],
            'grouping_category_id' => ['nullable', 'exists:grouping_categories,id'],
            'condition_ids' => ['required', 'array', 'min:1'],
            'condition_ids.*' => ['exists:conditions,id'],
            'destination_ids' => ['nullable', 'array'],
            'destination_ids.*' => ['exists:destinations,id'],
            'description' => ['required', 'string'],
            'provenance' => ['nullable', 'string'],
            'epos' => ['nullable', 'string', 'max:255'],
            'starting_price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
            'catalog_entries' => ['nullable', 'array'],
            'catalog_entries.*.catalog_type_id' => ['required', 'exists:catalog_types,id'],
            'catalog_entries.*.catalog_number' => ['required', 'string', 'max:255'],
            'packages' => ['nullable', 'array'],
            'packages.*.pack_type_id' => ['required', 'exists:pack_types,id'],
            'packages.*.pack_number' => ['required', 'string', 'max:255'],
            'packages.*.pack_note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Requests/
git commit -m "feat: Validierungsregeln fuer erweiterte Losfelder (ref #3)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 7: Describer LotController aktualisieren

**Files:**
- Modify: `app/Http/Controllers/Describer/LotController.php`
- Modify: `tests/Feature/Describer/LotManagementTest.php`

- [ ] **Step 1: Update LotManagementTest**

Replace `tests/Feature/Describer/LotManagementTest.php`:

```php
<?php

namespace Tests\Feature\Describer;

use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Condition;
use App\Models\Consignment;
use App\Models\Destination;
use App\Models\Lot;
use App\Models\PackType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LotManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function createLotData(): array
    {
        return [
            'lot_type' => 'single',
            'category_ids' => [Category::factory()->create()->id],
            'condition_ids' => [Condition::factory()->create()->id],
            'description' => 'Dt. Reich Mi.Nr. 1-3 gestempelt',
            'starting_price' => 150.00,
            'catalog_entries' => [
                ['catalog_type_id' => CatalogType::factory()->create()->id, 'catalog_number' => '1-3'],
            ],
        ];
    }

    public function test_describer_can_create_lot(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id, 'start_number' => 1, 'next_number' => 1]);

        $response = $this->actingAs($user)->post("/consignments/{$consignment->id}/lots", $this->createLotData());

        $response->assertRedirect();
        $this->assertDatabaseHas('lots', ['consignment_id' => $consignment->id, 'sequence_number' => 1, 'lot_type' => 'single']);
        $this->assertEquals(2, $consignment->fresh()->next_number);
        $this->assertCount(1, Lot::first()->categories);
        $this->assertCount(1, Lot::first()->conditions);
        $this->assertCount(1, Lot::first()->catalogEntries);
    }

    public function test_sequence_number_auto_increments(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id, 'start_number' => 5, 'next_number' => 5]);

        $this->actingAs($user)->post("/consignments/{$consignment->id}/lots", $this->createLotData());
        $this->actingAs($user)->post("/consignments/{$consignment->id}/lots", $this->createLotData());

        $lots = $consignment->fresh()->lots->sortBy('sequence_number');
        $this->assertEquals(5, $lots->first()->sequence_number);
        $this->assertEquals(6, $lots->last()->sequence_number);
        $this->assertEquals(7, $consignment->fresh()->next_number);
    }

    public function test_describer_can_update_own_lot(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id]);
        $lot = Lot::factory()->create(['consignment_id' => $consignment->id]);
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();
        $lot->categories()->attach($category);
        $lot->conditions()->attach($condition);

        $newData = $this->createLotData();
        $newData['description'] = 'Updated description';

        $response = $this->actingAs($user)->put(
            "/consignments/{$consignment->id}/lots/{$lot->id}",
            $newData
        );

        $response->assertRedirect();
        $this->assertEquals('Updated description', $lot->fresh()->description);
    }

    public function test_describer_cannot_create_lot_on_closed_consignment(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id, 'status' => 'closed']);

        $response = $this->actingAs($user)->post("/consignments/{$consignment->id}/lots", $this->createLotData());
        $response->assertStatus(403);
    }

    public function test_describer_cannot_modify_lot_on_other_users_consignment(): void
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user2->id]);
        $lot = Lot::factory()->create(['consignment_id' => $consignment->id]);

        $response = $this->actingAs($user1)->put("/consignments/{$consignment->id}/lots/{$lot->id}", $this->createLotData());
        $response->assertStatus(403);
    }

    public function test_describer_can_delete_lot(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id]);
        $lot = Lot::factory()->create(['consignment_id' => $consignment->id]);

        $response = $this->actingAs($user)->delete("/consignments/{$consignment->id}/lots/{$lot->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('lots', ['id' => $lot->id]);
    }

    public function test_lot_with_packages_and_destinations(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id, 'next_number' => 1]);
        $packType = PackType::factory()->create();
        $destination = Destination::factory()->create();

        $data = array_merge($this->createLotData(), [
            'destination_ids' => [$destination->id],
            'provenance' => 'Sammlung Müller',
            'epos' => 'E-123',
            'packages' => [
                ['pack_type_id' => $packType->id, 'pack_number' => '14', 'pack_note' => 'Oben links'],
            ],
        ]);

        $response = $this->actingAs($user)->post("/consignments/{$consignment->id}/lots", $data);
        $response->assertRedirect();

        $lot = Lot::first();
        $this->assertCount(1, $lot->destinations);
        $this->assertCount(1, $lot->packages);
        $this->assertEquals('Sammlung Müller', $lot->provenance);
        $this->assertEquals('E-123', $lot->epos);
    }
}
```

- [ ] **Step 2: Run tests — should fail**

```bash
php artisan test tests/Feature/Describer/LotManagementTest.php
```

Expected: FAIL.

- [ ] **Step 3: Update Describer LotController**

Replace `app/Http/Controllers/Describer/LotController.php`:

```php
<?php

namespace App\Http\Controllers\Describer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLotRequest;
use App\Http\Requests\UpdateLotRequest;
use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Condition;
use App\Models\Consignment;
use App\Models\Destination;
use App\Models\GroupingCategory;
use App\Models\Lot;
use App\Models\PackType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class LotController extends Controller
{
    public function store(StoreLotRequest $request, Consignment $consignment)
    {
        DB::transaction(function () use ($request, $consignment) {
            $lot = $consignment->lots()->create(array_merge(
                $request->safe()->only(['lot_type', 'grouping_category_id', 'description', 'provenance', 'epos', 'starting_price', 'notes']),
                ['sequence_number' => $consignment->next_number]
            ));

            $lot->categories()->sync($request->category_ids);
            $lot->conditions()->sync($request->condition_ids);

            if ($request->destination_ids) {
                $lot->destinations()->sync($request->destination_ids);
            }

            if ($request->catalog_entries) {
                foreach ($request->catalog_entries as $entry) {
                    $lot->catalogEntries()->create($entry);
                }
            }

            if ($request->packages) {
                foreach ($request->packages as $package) {
                    $lot->packages()->create($package);
                }
            }

            $consignment->increment('next_number');
        });

        return redirect()
            ->route('describer.consignments.show', $consignment)
            ->with('success', __('messages.lot_created'));
    }

    public function edit(Consignment $consignment, Lot $lot)
    {
        Gate::authorize('manageLots', $consignment);
        $lot->load(['categories', 'conditions', 'destinations', 'catalogEntries.catalogType', 'packages.packType', 'groupingCategory']);

        return view('describer.lots.edit', [
            'consignment' => $consignment,
            'lot' => $lot,
            'categories' => Category::all(),
            'catalogTypes' => CatalogType::all(),
            'conditions' => Condition::all(),
            'destinations' => Destination::all(),
            'groupingCategories' => GroupingCategory::all(),
            'packTypes' => PackType::all(),
        ]);
    }

    public function update(UpdateLotRequest $request, Consignment $consignment, Lot $lot)
    {
        DB::transaction(function () use ($request, $lot) {
            $lot->update($request->safe()->only(['lot_type', 'grouping_category_id', 'description', 'provenance', 'epos', 'starting_price', 'notes']));

            $lot->categories()->sync($request->category_ids);
            $lot->conditions()->sync($request->condition_ids);
            $lot->destinations()->sync($request->destination_ids ?? []);

            $lot->catalogEntries()->delete();
            if ($request->catalog_entries) {
                foreach ($request->catalog_entries as $entry) {
                    $lot->catalogEntries()->create($entry);
                }
            }

            $lot->packages()->delete();
            if ($request->packages) {
                foreach ($request->packages as $package) {
                    $lot->packages()->create($package);
                }
            }
        });

        return redirect()
            ->route('describer.consignments.show', $consignment)
            ->with('success', __('messages.lot_updated'));
    }

    public function destroy(Consignment $consignment, Lot $lot)
    {
        Gate::authorize('manageLots', $consignment);
        $lot->delete();

        return redirect()
            ->route('describer.consignments.show', $consignment)
            ->with('success', __('messages.lot_deleted'));
    }
}
```

- [ ] **Step 4: Run tests — should pass**

```bash
php artisan test tests/Feature/Describer/LotManagementTest.php
```

Expected: 7 tests PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Describer/LotController.php tests/Feature/Describer/LotManagementTest.php
git commit -m "feat: Describer LotController mit Pivot-Sync und Detail-Tabellen (ref #3)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 8: API LotController aktualisieren

**Files:**
- Modify: `app/Http/Controllers/Api/LotController.php`
- Modify: `tests/Feature/Api/LotApiTest.php`

- [ ] **Step 1: Update LotApiTest**

Replace `tests/Feature/Api/LotApiTest.php`:

```php
<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Condition;
use App\Models\Consignment;
use App\Models\Destination;
use App\Models\Lot;
use App\Models\LotCatalogEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LotApiTest extends TestCase
{
    use RefreshDatabase;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.api.key' => 'test-api-key']);
        $this->headers = ['X-API-Key' => 'test-api-key'];
    }

    public function test_list_lots_for_consignment(): void
    {
        $consignment = Consignment::factory()->create();
        $lot = Lot::factory()->create(['consignment_id' => $consignment->id]);
        $lot->categories()->attach(Category::factory()->create());
        $lot->conditions()->attach(Condition::factory()->create());

        $response = $this->getJson("/api/v1/consignments/{$consignment->id}/lots", $this->headers);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonStructure(['data' => [['id', 'lot_type', 'categories', 'conditions', 'destinations', 'catalog_entries', 'packages']]]);
    }

    public function test_filter_lots_by_consignor_number(): void
    {
        $c1 = Consignment::factory()->create(['consignor_number' => '1111111']);
        $c2 = Consignment::factory()->create(['consignor_number' => '2222222']);
        Lot::factory()->count(3)->create(['consignment_id' => $c1->id]);
        Lot::factory()->count(2)->create(['consignment_id' => $c2->id]);

        $response = $this->getJson('/api/v1/lots?consignor_number=1111111', $this->headers);

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_lot_response_includes_all_relations(): void
    {
        $consignment = Consignment::factory()->create();
        $lot = Lot::factory()->create([
            'consignment_id' => $consignment->id,
            'provenance' => 'Test provenance',
            'epos' => 'E-999',
        ]);
        $lot->categories()->attach(Category::factory()->create());
        $lot->conditions()->attach(Condition::factory()->create());
        $lot->destinations()->attach(Destination::factory()->create());
        LotCatalogEntry::factory()->create(['lot_id' => $lot->id]);

        $response = $this->getJson("/api/v1/consignments/{$consignment->id}/lots", $this->headers);

        $data = $response->json('data.0');
        $this->assertEquals('Test provenance', $data['provenance']);
        $this->assertEquals('E-999', $data['epos']);
        $this->assertCount(1, $data['categories']);
        $this->assertCount(1, $data['conditions']);
        $this->assertCount(1, $data['destinations']);
        $this->assertCount(1, $data['catalog_entries']);
    }
}
```

- [ ] **Step 2: Run tests — should fail**

```bash
php artisan test tests/Feature/Api/LotApiTest.php
```

- [ ] **Step 3: Update API LotController**

Replace `app/Http/Controllers/Api/LotController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consignment;
use App\Models\Lot;
use Illuminate\Http\Request;

class LotController extends Controller
{
    private array $lotRelations = [
        'categories',
        'conditions',
        'destinations',
        'catalogEntries.catalogType',
        'packages.packType',
        'groupingCategory',
    ];

    public function index(Consignment $consignment)
    {
        $lots = $consignment->lots()
            ->with($this->lotRelations)
            ->orderBy('sequence_number')
            ->get();

        return response()->json(['data' => $lots]);
    }

    public function byConsignorNumber(Request $request)
    {
        $request->validate(['consignor_number' => ['required', 'string']]);

        $lots = Lot::with(array_merge($this->lotRelations, ['consignment']))
            ->whereHas('consignment', fn($q) => $q->where('consignor_number', $request->consignor_number))
            ->orderBy('sequence_number')
            ->get();

        return response()->json(['data' => $lots]);
    }
}
```

- [ ] **Step 4: Run tests — should pass**

```bash
php artisan test tests/Feature/Api/LotApiTest.php
```

Expected: 3 tests PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Api/LotController.php tests/Feature/Api/LotApiTest.php
git commit -m "feat: API LotController mit erweiterten Relations (ref #3)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 9: Livewire LotForm komplett neu

**Files:**
- Modify: `app/Livewire/LotForm.php` (full rewrite)
- Modify: `resources/views/livewire/lot-form.blade.php` (full rewrite)

This is the most complex task. The Livewire component handles:
- Multi-select filter-as-you-type for categories, destinations
- Single-select filter-as-you-type for grouping category
- Toggle buttons for conditions
- Dynamic catalog entries and packages
- HTML editor for description and provenance (using Trix)
- Radio buttons for lot type

- [ ] **Step 1: Install Trix editor**

```bash
npm install trix
```

Add to `resources/js/app.js`:
```js
import 'trix';
```

- [ ] **Step 2: Rewrite LotForm Livewire component**

Replace `app/Livewire/LotForm.php` with the complete component. This file manages all form state, search/filter logic for dropdowns, dynamic entries for catalog and packages, and condition toggle. The component submits via standard form POST (not Livewire submit) to the existing LotController::store route.

Key responsibilities:
- Category multi-select with search
- Destination multi-select with search
- Grouping category single-select with search
- Condition toggle (array of selected IDs)
- Dynamic catalog entries (add/remove rows)
- Dynamic packages (add/remove rows)
- Hidden inputs for all values to submit via POST

- [ ] **Step 3: Rewrite lot-form.blade.php**

The Blade template implements the exact layout from the design spec (formular-v6.html):

Zeile 1: Kategorien (2/3) + Losart (1/3)
Zeile 2: Gruppe (2/3) + Startpreis (1/3)
Zeile 3: Losbeschreibung (100%, Trix editor)
Zeile 4: Provenance (100%, Trix editor)
Zeile 5: Erhaltung (label left, buttons right)
Zeile 6: Destination (2/3) + EPos (1/3)
Zeile 7: Katalogeinträge (50%) + Verpackung (50%)
Zeile 8: Bemerkung (100%)
Buttons: Abbrechen + Speichern & Nächstes

Use TailwindCSS classes matching the design. Reference: .superpowers/brainstorm/440-1776090335/content/formular-v6.html

- [ ] **Step 4: Build assets**

```bash
npm run build
```

- [ ] **Step 5: Commit**

```bash
git add app/Livewire/LotForm.php resources/views/livewire/lot-form.blade.php resources/js/app.js package.json package-lock.json
git commit -m "feat: Livewire LotForm komplett neu mit allen erweiterten Feldern (ref #3)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 10: Views aktualisieren

**Files:**
- Modify: `resources/views/describer/consignments/show.blade.php`
- Modify: `resources/views/describer/lots/edit.blade.php` (full rewrite)
- Modify: `resources/lang/de/messages.php`
- Modify: `resources/lang/en/messages.php`

- [ ] **Step 1: Add new language keys**

Add to `resources/lang/de/messages.php`:
```php
'lot_type' => 'Losart',
'single_lot' => 'Einzellos',
'collection' => 'Sammlung',
'grouping_category' => 'Gruppe',
'conditions' => 'Erhaltung',
'condition_min_one' => 'mind. 1',
'destination' => 'Destination',
'provenance' => 'Provenance',
'epos' => 'EPos',
'epos_hint' => 'Einlieferer-Referenz',
'catalog_entries' => 'Katalogeinträge',
'add_catalog_entry' => '+ Katalogeintrag',
'packaging' => 'Verpackung',
'pack_type' => 'Packtype',
'pack_number' => 'Nr.',
'pack_note' => 'Bemerkung',
'add_package' => '+ Packstück',
'multiple_possible' => 'mehrere möglich',
'herkunft_vorbesitzer' => 'Herkunft / Vorbesitzer',
```

Add equivalent English keys to `resources/lang/en/messages.php`:
```php
'lot_type' => 'Lot Type',
'single_lot' => 'Single Lot',
'collection' => 'Collection',
'grouping_category' => 'Group',
'conditions' => 'Condition',
'condition_min_one' => 'min. 1',
'destination' => 'Destination',
'provenance' => 'Provenance',
'epos' => 'EPos',
'epos_hint' => 'Consignor Reference',
'catalog_entries' => 'Catalog Entries',
'add_catalog_entry' => '+ Catalog Entry',
'packaging' => 'Packaging',
'pack_type' => 'Pack Type',
'pack_number' => 'No.',
'pack_note' => 'Note',
'add_package' => '+ Package',
'multiple_possible' => 'multiple possible',
'herkunft_vorbesitzer' => 'Origin / Previous Owner',
```

- [ ] **Step 2: Update show.blade.php lot table**

Update the lot table in `resources/views/describer/consignments/show.blade.php` to show the new fields. Replace the table columns:

- Lfd.Nr. (unchanged)
- Losart (new)
- Kategorien (comma-separated names instead of single category)
- Beschreibung (truncated, strip HTML tags for display)
- Erhaltung (condition names joined)
- Startpreis (unchanged)
- Actions (unchanged)

- [ ] **Step 3: Rewrite edit.blade.php**

Rewrite `resources/views/describer/lots/edit.blade.php` to match the same layout as the Livewire form but as a standard form with PUT method. Pre-populate all fields from the lot's current data. Use standard select/input elements (no Livewire needed for edit).

- [ ] **Step 4: Commit**

```bash
git add resources/views/ resources/lang/
git commit -m "feat: Views und Sprachdateien fuer erweiterte Losfelder (ref #3)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 11: API-Dokumentation aktualisieren

**Files:**
- Modify: GitHub Issue #2

- [ ] **Step 1: Update API docs issue**

Add a comment to GitHub Issue #2 (GlobalPhilatelicNetwork/externe-beschreiber#2) documenting:

1. All new Lookup CRUD endpoints (categories, catalog-types, grouping-categories, destinations)
2. Read-only endpoints (conditions, pack-types, lot-types)
3. Updated lot response structure with all new nested fields
4. Updated lot creation/update request format

Include both human-readable and technical reference sections matching the format of the existing issue.

- [ ] **Step 2: Commit (no code change, just issue update)**

No commit needed — this is an issue-only change.

---

### Task 12: Finaler Test-Lauf & Aufräumen

**Files:**
- All test files

- [ ] **Step 1: Run complete test suite**

```bash
php artisan test
```

Fix any failing tests.

- [ ] **Step 2: Check for missing language keys**

Verify all `__('messages.xxx')` references in views have corresponding entries in both language files.

- [ ] **Step 3: Push to remote**

```bash
git push origin master
```

---

## Zusammenfassung

| Task | Beschreibung | Tests |
|------|-------------|-------|
| 1 | Neue Migrationen (10 Dateien) | — |
| 2 | Neue Models & Factories | — |
| 3 | Lot Model & Factory aktualisieren | 8 Unit-Tests |
| 4 | Seeders | — |
| 5 | Lookup API CRUD | 14 Feature-Tests |
| 6 | Validation Requests | — |
| 7 | Describer LotController | 7 Feature-Tests |
| 8 | API LotController | 3 Feature-Tests |
| 9 | Livewire LotForm (komplett neu) | — |
| 10 | Views & Sprachdateien | — |
| 11 | API-Doku Issue updaten | — |
| 12 | Finaler Test-Lauf | Alle |

**Gesamt: 12 Tasks, ~32 neue/geänderte Tests**
