# Externe Beschreiber Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a Laravel web app for external auction lot describers to capture lot descriptions, with admin management and a REST API for the dashboard.

**Architecture:** Laravel 11 with Blade/Livewire for the UI, MySQL database, two authentication layers (session for web, API-key for REST). Deployed on All-Inkl. shared hosting (PHP + MySQL, no Docker).

**Tech Stack:** Laravel 11, PHP 8.2+, MySQL, Livewire 3, Blade, TailwindCSS, PHPUnit

---

## File Structure

```
externe-beschreiber/
├── app/
│   ├── Models/
│   │   ├── User.php                          (modify Laravel default)
│   │   ├── Consignment.php                   (create)
│   │   ├── Lot.php                           (create)
│   │   ├── Category.php                      (create)
│   │   ├── CatalogType.php                   (create)
│   │   └── CatalogPart.php                   (create)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php       (create)
│   │   │   ├── Admin/
│   │   │   │   ├── UserController.php        (create)
│   │   │   │   └── ConsignmentController.php (create)
│   │   │   ├── Describer/
│   │   │   │   ├── ConsignmentController.php (create)
│   │   │   │   └── LotController.php         (create)
│   │   │   └── Api/
│   │   │       ├── ConsignmentController.php (create)
│   │   │       ├── LotController.php         (create)
│   │   │       └── UserController.php        (create)
│   │   ├── Middleware/
│   │   │   ├── RoleMiddleware.php            (create)
│   │   │   ├── ApiKeyMiddleware.php          (create)
│   │   │   └── ConsignmentOpenMiddleware.php (create)
│   │   └── Requests/
│   │       ├── StoreLotRequest.php           (create)
│   │       ├── UpdateLotRequest.php          (create)
│   │       ├── StoreConsignmentRequest.php   (create)
│   │       └── StoreUserRequest.php          (create)
│   ├── Mail/
│   │   └── CredentialsMail.php               (create)
│   ├── Livewire/
│   │   └── LotForm.php                       (create – filter-as-you-type)
│   └── Policies/
│       ├── ConsignmentPolicy.php             (create)
│       └── LotPolicy.php                     (create)
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php                 (create)
│   │   ├── auth/
│   │   │   └── login.blade.php               (create)
│   │   ├── admin/
│   │   │   ├── users/
│   │   │   │   └── index.blade.php           (create)
│   │   │   └── consignments/
│   │   │       └── index.blade.php           (create)
│   │   ├── describer/
│   │   │   ├── consignments/
│   │   │   │   ├── index.blade.php           (create)
│   │   │   │   └── show.blade.php            (create)
│   │   │   └── lots/
│   │   │       └── _form.blade.php           (create – Livewire component template)
│   │   └── emails/
│   │       └── credentials.blade.php         (create)
│   └── lang/
│       ├── de/
│       │   ├── auth.php                      (create)
│       │   ├── messages.php                  (create)
│       │   └── validation.php                (create)
│       └── en/
│           ├── auth.php                      (create)
│           ├── messages.php                  (create)
│           └── validation.php                (create)
├── routes/
│   ├── web.php                               (modify)
│   └── api.php                               (modify)
├── database/
│   ├── migrations/
│   │   ├── xxxx_modify_users_table.php       (create)
│   │   ├── xxxx_create_categories_table.php  (create)
│   │   ├── xxxx_create_catalog_types_table.php (create)
│   │   ├── xxxx_create_catalog_parts_table.php (create)
│   │   ├── xxxx_create_consignments_table.php  (create)
│   │   └── xxxx_create_lots_table.php        (create)
│   ├── seeders/
│   │   ├── DatabaseSeeder.php                (modify)
│   │   ├── LookupSeeder.php                  (create)
│   │   └── AdminSeeder.php                   (create)
│   └── factories/
│       ├── ConsignmentFactory.php            (create)
│       ├── LotFactory.php                    (create)
│       ├── CategoryFactory.php               (create)
│       ├── CatalogTypeFactory.php            (create)
│       └── CatalogPartFactory.php            (create)
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   │   └── LoginTest.php                 (create)
│   │   ├── Admin/
│   │   │   ├── UserManagementTest.php        (create)
│   │   │   └── ConsignmentManagementTest.php (create)
│   │   ├── Describer/
│   │   │   ├── ConsignmentListTest.php       (create)
│   │   │   └── LotManagementTest.php         (create)
│   │   ├── Api/
│   │   │   ├── ConsignmentApiTest.php        (create)
│   │   │   ├── LotApiTest.php                (create)
│   │   │   └── UserApiTest.php               (create)
│   │   └── Middleware/
│   │       ├── RoleMiddlewareTest.php        (create)
│   │       ├── ApiKeyMiddlewareTest.php      (create)
│   │       └── ConsignmentOpenMiddlewareTest.php (create)
│   └── Unit/
│       ├── Models/
│       │   ├── ConsignmentTest.php           (create)
│       │   └── LotTest.php                   (create)
│       └── Mail/
│           └── CredentialsMailTest.php       (create)
└── config/
    └── services.php                          (modify – add api_key config)
```

---

### Task 1: Laravel-Projekt aufsetzen

**Files:**
- Create: `externe-beschreiber/` (Laravel-Projekt via Composer)
- Modify: `.env`
- Modify: `config/services.php`

- [ ] **Step 1: Laravel-Projekt erstellen**

```bash
cd G:/Claude-Projekte/Externe_Beschreiber
composer create-project laravel/laravel externe-beschreiber
```

- [ ] **Step 2: In Projektverzeichnis wechseln und Livewire installieren**

```bash
cd G:/Claude-Projekte/Externe_Beschreiber/externe-beschreiber
composer require livewire/livewire
```

- [ ] **Step 3: `.env` konfigurieren**

Datei `.env` anpassen:

```env
APP_NAME="Externe Beschreiber"
APP_URL=http://localhost:8000
APP_LOCALE=de
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=externe_beschreiber
DB_USERNAME=root
DB_PASSWORD=

API_KEY=dev-api-key-change-in-production
```

- [ ] **Step 4: API-Key in Config registrieren**

In `config/services.php` hinzufügen:

```php
// am Ende des return-Arrays:
'api' => [
    'key' => env('API_KEY'),
],
```

- [ ] **Step 5: Datenbank erstellen und prüfen**

```bash
php artisan db:wipe --force
php artisan migrate
```

Expected: Migration erfolgreich, Default-Laravel-Tabellen erstellt.

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "init: Laravel 11 Projekt mit Livewire aufsetzen\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 2: Datenbank-Migrationen

**Files:**
- Create: `database/migrations/xxxx_modify_users_table.php`
- Create: `database/migrations/xxxx_create_categories_table.php`
- Create: `database/migrations/xxxx_create_catalog_types_table.php`
- Create: `database/migrations/xxxx_create_catalog_parts_table.php`
- Create: `database/migrations/xxxx_create_consignments_table.php`
- Create: `database/migrations/xxxx_create_lots_table.php`

- [ ] **Step 1: Users-Tabelle erweitern**

```bash
php artisan make:migration add_role_and_locale_to_users_table --table=users
```

Migration-Inhalt:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'user'])->default('user')->after('password');
        $table->enum('locale', ['de', 'en'])->default('de')->after('role');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['role', 'locale']);
    });
}
```

- [ ] **Step 2: Lookup-Tabellen erstellen**

```bash
php artisan make:migration create_categories_table
php artisan make:migration create_catalog_types_table
php artisan make:migration create_catalog_parts_table
```

`create_categories_table`:

```php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name_de');
        $table->string('name_en');
        $table->timestamps();
    });
}
```

`create_catalog_types_table`:

```php
public function up(): void
{
    Schema::create('catalog_types', function (Blueprint $table) {
        $table->id();
        $table->string('name_de');
        $table->string('name_en');
        $table->timestamps();
    });
}
```

`create_catalog_parts_table`:

```php
public function up(): void
{
    Schema::create('catalog_parts', function (Blueprint $table) {
        $table->id();
        $table->string('name_de');
        $table->string('name_en');
        $table->boolean('is_default')->default(false);
        $table->timestamps();
    });
}
```

- [ ] **Step 3: Consignments-Tabelle erstellen**

```bash
php artisan make:migration create_consignments_table
```

```php
public function up(): void
{
    Schema::create('consignments', function (Blueprint $table) {
        $table->id();
        $table->string('consignor_number');
        $table->string('internal_nid');
        $table->unsignedInteger('start_number');
        $table->unsignedInteger('next_number');
        $table->foreignId('catalog_part_id')->constrained('catalog_parts');
        $table->foreignId('user_id')->constrained('users');
        $table->enum('status', ['open', 'closed'])->default('open');
        $table->timestamps();
    });
}
```

- [ ] **Step 4: Lots-Tabelle erstellen**

```bash
php artisan make:migration create_lots_table
```

```php
public function up(): void
{
    Schema::create('lots', function (Blueprint $table) {
        $table->id();
        $table->foreignId('consignment_id')->constrained('consignments')->cascadeOnDelete();
        $table->unsignedInteger('sequence_number');
        $table->foreignId('category_id')->constrained('categories');
        $table->text('description');
        $table->foreignId('catalog_type_id')->constrained('catalog_types');
        $table->string('catalog_number');
        $table->decimal('starting_price', 10, 2);
        $table->string('notes')->nullable();
        $table->timestamps();

        $table->unique(['consignment_id', 'sequence_number']);
    });
}
```

- [ ] **Step 5: Migrationen ausführen**

```bash
php artisan migrate:fresh
```

Expected: Alle Tabellen erfolgreich erstellt.

- [ ] **Step 6: Commit**

```bash
git add database/migrations/
git commit -m "feat: Datenbank-Migrationen für alle Tabellen (ref #TBD)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 3: Models & Factories

**Files:**
- Modify: `app/Models/User.php`
- Create: `app/Models/Consignment.php`
- Create: `app/Models/Lot.php`
- Create: `app/Models/Category.php`
- Create: `app/Models/CatalogType.php`
- Create: `app/Models/CatalogPart.php`
- Create: `database/factories/ConsignmentFactory.php`
- Create: `database/factories/LotFactory.php`
- Create: `database/factories/CategoryFactory.php`
- Create: `database/factories/CatalogTypeFactory.php`
- Create: `database/factories/CatalogPartFactory.php`
- Test: `tests/Unit/Models/ConsignmentTest.php`
- Test: `tests/Unit/Models/LotTest.php`

- [ ] **Step 1: Tests für Consignment-Model schreiben**

Datei `tests/Unit/Models/ConsignmentTest.php`:

```php
<?php

namespace Tests\Unit\Models;

use App\Models\Consignment;
use App\Models\Lot;
use App\Models\User;
use App\Models\CatalogPart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_consignment_belongs_to_user(): void
    {
        $consignment = Consignment::factory()->create();

        $this->assertInstanceOf(User::class, $consignment->user);
    }

    public function test_consignment_belongs_to_catalog_part(): void
    {
        $consignment = Consignment::factory()->create();

        $this->assertInstanceOf(CatalogPart::class, $consignment->catalogPart);
    }

    public function test_consignment_has_many_lots(): void
    {
        $consignment = Consignment::factory()->create();
        Lot::factory()->count(3)->create(['consignment_id' => $consignment->id]);

        $this->assertCount(3, $consignment->lots);
    }

    public function test_consignment_is_open_by_default(): void
    {
        $consignment = Consignment::factory()->create();

        $this->assertEquals('open', $consignment->status);
    }

    public function test_consignment_can_be_closed(): void
    {
        $consignment = Consignment::factory()->create();
        $consignment->update(['status' => 'closed']);

        $this->assertEquals('closed', $consignment->fresh()->status);
    }

    public function test_is_open_returns_correct_boolean(): void
    {
        $open = Consignment::factory()->create(['status' => 'open']);
        $closed = Consignment::factory()->create(['status' => 'closed']);

        $this->assertTrue($open->isOpen());
        $this->assertFalse($closed->isOpen());
    }
}
```

- [ ] **Step 2: Tests für Lot-Model schreiben**

Datei `tests/Unit/Models/LotTest.php`:

```php
<?php

namespace Tests\Unit\Models;

use App\Models\Lot;
use App\Models\Consignment;
use App\Models\Category;
use App\Models\CatalogType;
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

    public function test_lot_belongs_to_category(): void
    {
        $lot = Lot::factory()->create();

        $this->assertInstanceOf(Category::class, $lot->category);
    }

    public function test_lot_belongs_to_catalog_type(): void
    {
        $lot = Lot::factory()->create();

        $this->assertInstanceOf(CatalogType::class, $lot->catalogType);
    }
}
```

- [ ] **Step 3: Tests ausführen — sollen fehlschlagen**

```bash
php artisan test tests/Unit/Models/
```

Expected: FAIL — Models und Factories existieren noch nicht.

- [ ] **Step 4: User-Model erweitern**

In `app/Models/User.php` anpassen:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function consignments(): HasMany
    {
        return $this->hasMany(Consignment::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
```

- [ ] **Step 5: Lookup-Models erstellen**

`app/Models/Category.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name_de', 'name_en'];

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }
}
```

`app/Models/CatalogType.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogType extends Model
{
    use HasFactory;

    protected $fillable = ['name_de', 'name_en'];

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }
}
```

`app/Models/CatalogPart.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogPart extends Model
{
    use HasFactory;

    protected $fillable = ['name_de', 'name_en', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }
}
```

- [ ] **Step 6: Consignment-Model erstellen**

`app/Models/Consignment.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignor_number',
        'internal_nid',
        'start_number',
        'next_number',
        'catalog_part_id',
        'user_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function catalogPart(): BelongsTo
    {
        return $this->belongsTo(CatalogPart::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
```

- [ ] **Step 7: Lot-Model erstellen**

`app/Models/Lot.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignment_id',
        'sequence_number',
        'category_id',
        'description',
        'catalog_type_id',
        'catalog_number',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function catalogType(): BelongsTo
    {
        return $this->belongsTo(CatalogType::class);
    }
}
```

- [ ] **Step 8: Factories erstellen**

`database/factories/CategoryFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name_de' => fake()->word(),
            'name_en' => fake()->word(),
        ];
    }
}
```

`database/factories/CatalogTypeFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\CatalogType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatalogTypeFactory extends Factory
{
    protected $model = CatalogType::class;

    public function definition(): array
    {
        return [
            'name_de' => fake()->word(),
            'name_en' => fake()->word(),
        ];
    }
}
```

`database/factories/CatalogPartFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\CatalogPart;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatalogPartFactory extends Factory
{
    protected $model = CatalogPart::class;

    public function definition(): array
    {
        return [
            'name_de' => 'Hauptkatalog',
            'name_en' => 'Main Catalog',
            'is_default' => true,
        ];
    }
}
```

`database/factories/ConsignmentFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\CatalogPart;
use App\Models\Consignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsignmentFactory extends Factory
{
    protected $model = Consignment::class;

    public function definition(): array
    {
        return [
            'consignor_number' => (string) fake()->numerify('#######'),
            'internal_nid' => (string) fake()->numerify('####'),
            'start_number' => 1,
            'next_number' => 1,
            'catalog_part_id' => CatalogPart::factory(),
            'user_id' => User::factory(),
            'status' => 'open',
        ];
    }

    public function closed(): static
    {
        return $this->state(['status' => 'closed']);
    }
}
```

`database/factories/LotFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CatalogType;
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
            'sequence_number' => fake()->unique()->numberBetween(1, 9999),
            'category_id' => Category::factory(),
            'description' => fake()->sentence(10),
            'catalog_type_id' => CatalogType::factory(),
            'catalog_number' => fake()->numerify('###'),
            'starting_price' => fake()->randomFloat(2, 10, 5000),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
```

- [ ] **Step 9: Tests ausführen — sollen bestehen**

```bash
php artisan test tests/Unit/Models/
```

Expected: Alle 9 Tests PASS.

- [ ] **Step 10: Commit**

```bash
git add app/Models/ database/factories/ tests/Unit/Models/
git commit -m "feat: Models, Factories und Unit-Tests (ref #TBD)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 4: Seeders

**Files:**
- Create: `database/seeders/LookupSeeder.php`
- Create: `database/seeders/AdminSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: LookupSeeder erstellen**

`database/seeders/LookupSeeder.php`:

```php
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

        foreach ($categories as $cat) {
            Category::firstOrCreate($cat);
        }

        $catalogTypes = [
            ['name_de' => 'Michel', 'name_en' => 'Michel'],
            ['name_de' => 'Scott', 'name_en' => 'Scott'],
            ['name_de' => 'Yvert', 'name_en' => 'Yvert'],
            ['name_de' => 'Stanley Gibbons', 'name_en' => 'Stanley Gibbons'],
            ['name_de' => 'Zumstein', 'name_en' => 'Zumstein'],
        ];

        foreach ($catalogTypes as $ct) {
            CatalogType::firstOrCreate($ct);
        }

        $catalogParts = [
            ['name_de' => 'Hauptkatalog', 'name_en' => 'Main Catalog', 'is_default' => true],
            ['name_de' => 'Nebenkatalog', 'name_en' => 'Secondary Catalog', 'is_default' => false],
        ];

        foreach ($catalogParts as $cp) {
            CatalogPart::firstOrCreate(
                ['name_de' => $cp['name_de']],
                $cp
            );
        }
    }
}
```

- [ ] **Step 2: AdminSeeder erstellen**

`database/seeders/AdminSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@hkwi.auction'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('changeme'),
                'role' => 'admin',
                'locale' => 'de',
            ]
        );
    }
}
```

- [ ] **Step 3: DatabaseSeeder anpassen**

`database/seeders/DatabaseSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LookupSeeder::class,
            AdminSeeder::class,
        ]);
    }
}
```

- [ ] **Step 4: Seeder ausführen und prüfen**

```bash
php artisan migrate:fresh --seed
```

Expected: Alle Tabellen erstellt, Lookup-Daten und Admin-Account vorhanden.

- [ ] **Step 5: Commit**

```bash
git add database/seeders/
git commit -m "feat: Seeders für Lookup-Daten und Admin-Account (ref #TBD)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 5: Middleware

**Files:**
- Create: `app/Http/Middleware/RoleMiddleware.php`
- Create: `app/Http/Middleware/ApiKeyMiddleware.php`
- Create: `app/Http/Middleware/ConsignmentOpenMiddleware.php`
- Test: `tests/Feature/Middleware/RoleMiddlewareTest.php`
- Test: `tests/Feature/Middleware/ApiKeyMiddlewareTest.php`
- Test: `tests/Feature/Middleware/ConsignmentOpenMiddlewareTest.php`

- [ ] **Step 1: Tests für RoleMiddleware schreiben**

`tests/Feature/Middleware/RoleMiddlewareTest.php`:

```php
<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
    }

    public function test_describer_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('/login');
    }
}
```

- [ ] **Step 2: Tests für ApiKeyMiddleware schreiben**

`tests/Feature/Middleware/ApiKeyMiddlewareTest.php`:

```php
<?php

namespace Tests\Feature\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiKeyMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_api_key_allows_access(): void
    {
        config(['services.api.key' => 'test-api-key']);

        $response = $this->getJson('/api/v1/consignments', [
            'X-API-Key' => 'test-api-key',
        ]);

        $response->assertStatus(200);
    }

    public function test_missing_api_key_returns_401(): void
    {
        $response = $this->getJson('/api/v1/consignments');

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid API key']);
    }

    public function test_wrong_api_key_returns_401(): void
    {
        config(['services.api.key' => 'test-api-key']);

        $response = $this->getJson('/api/v1/consignments', [
            'X-API-Key' => 'wrong-key',
        ]);

        $response->assertStatus(401);
    }
}
```

- [ ] **Step 3: Tests für ConsignmentOpenMiddleware schreiben**

`tests/Feature/Middleware/ConsignmentOpenMiddlewareTest.php`:

```php
<?php

namespace Tests\Feature\Middleware;

use App\Models\Consignment;
use App\Models\User;
use App\Models\Category;
use App\Models\CatalogType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsignmentOpenMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_lot_to_open_consignment(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
            'next_number' => 1,
        ]);
        $category = Category::factory()->create();
        $catalogType = CatalogType::factory()->create();

        $response = $this->actingAs($user)->post("/consignments/{$consignment->id}/lots", [
            'category_id' => $category->id,
            'description' => 'Test lot description',
            'catalog_type_id' => $catalogType->id,
            'catalog_number' => '123',
            'starting_price' => 100.00,
        ]);

        $response->assertRedirect();
    }

    public function test_cannot_add_lot_to_closed_consignment(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create([
            'user_id' => $user->id,
            'status' => 'closed',
        ]);

        $response = $this->actingAs($user)->post("/consignments/{$consignment->id}/lots", [
            'category_id' => 1,
            'description' => 'Test',
            'catalog_type_id' => 1,
            'catalog_number' => '123',
            'starting_price' => 100.00,
        ]);

        $response->assertStatus(403);
    }
}
```

- [ ] **Step 4: Tests ausführen — sollen fehlschlagen**

```bash
php artisan test tests/Feature/Middleware/
```

Expected: FAIL — Middleware, Routen und Controller existieren noch nicht.

- [ ] **Step 5: RoleMiddleware implementieren**

`app/Http/Middleware/RoleMiddleware.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->role !== $role) {
            abort(403);
        }

        return $next($request);
    }
}
```

- [ ] **Step 6: ApiKeyMiddleware implementieren**

`app/Http/Middleware/ApiKeyMiddleware.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('services.api.key');

        if (!$apiKey || $request->header('X-API-Key') !== $apiKey) {
            return response()->json(['message' => 'Invalid API key'], 401);
        }

        return $next($request);
    }
}
```

- [ ] **Step 7: ConsignmentOpenMiddleware implementieren**

`app/Http/Middleware/ConsignmentOpenMiddleware.php`:

```php
<?php

namespace App\Http\Middleware;

use App\Models\Consignment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConsignmentOpenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $consignment = $request->route('consignment');

        if ($consignment instanceof Consignment && !$consignment->isOpen()) {
            abort(403, 'Consignment is closed');
        }

        return $next($request);
    }
}
```

- [ ] **Step 8: Middleware in `bootstrap/app.php` registrieren**

In `bootstrap/app.php` die Middleware-Aliase registrieren:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
        'consignment.open' => \App\Http\Middleware\ConsignmentOpenMiddleware::class,
    ]);
})
```

- [ ] **Step 9: Commit**

```bash
git add app/Http/Middleware/ bootstrap/app.php tests/Feature/Middleware/
git commit -m "feat: Middleware für Rollen, API-Key und Einlieferungs-Status (ref #TBD)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

Hinweis: Die Middleware-Tests werden erst vollständig grün, wenn die Routen und Controller (Tasks 6-9) implementiert sind. Das ist erwartetes Verhalten — die Tests validieren das Zusammenspiel von Middleware + Routen.

---

### Task 6: Authentifizierung (Login/Logout)

**Files:**
- Create: `app/Http/Controllers/Auth/LoginController.php`
- Create: `resources/views/auth/login.blade.php`
- Create: `resources/views/layouts/app.blade.php`
- Create: `resources/lang/de/auth.php`
- Create: `resources/lang/de/messages.php`
- Create: `resources/lang/en/auth.php`
- Create: `resources/lang/en/messages.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Auth/LoginTest.php`

- [ ] **Step 1: Login-Tests schreiben**

`tests/Feature/Auth/LoginTest.php`:

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/consignments');
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin/consignments');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);

        $response = $this->actingAs($user)->post('/password/change', [
            'current_password' => 'old-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertRedirect();
        $this->assertTrue(\Hash::check('new-password123', $user->fresh()->password));
    }

    public function test_locale_is_set_on_login(): void
    {
        $user = User::factory()->create([
            'locale' => 'en',
            'password' => bcrypt('password123'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertEquals('en', session('locale'));
    }
}
```

- [ ] **Step 2: Tests ausführen — sollen fehlschlagen**

```bash
php artisan test tests/Feature/Auth/LoginTest.php
```

Expected: FAIL — Controller und Views fehlen.

- [ ] **Step 3: Sprachdateien erstellen**

`resources/lang/de/auth.php`:

```php
<?php

return [
    'failed' => 'Die angegebenen Zugangsdaten sind ungültig.',
    'throttle' => 'Zu viele Anmeldeversuche. Bitte versuchen Sie es in :seconds Sekunden erneut.',
    'login' => 'Anmelden',
    'logout' => 'Abmelden',
    'email' => 'E-Mail',
    'password' => 'Passwort',
    'remember_me' => 'Angemeldet bleiben',
    'change_password' => 'Passwort ändern',
    'current_password' => 'Aktuelles Passwort',
    'new_password' => 'Neues Passwort',
    'confirm_password' => 'Passwort bestätigen',
    'password_changed' => 'Passwort erfolgreich geändert.',
];
```

`resources/lang/en/auth.php`:

```php
<?php

return [
    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'login' => 'Login',
    'logout' => 'Logout',
    'email' => 'Email',
    'password' => 'Password',
    'remember_me' => 'Remember me',
    'change_password' => 'Change Password',
    'current_password' => 'Current Password',
    'new_password' => 'New Password',
    'confirm_password' => 'Confirm Password',
    'password_changed' => 'Password changed successfully.',
];
```

`resources/lang/de/messages.php`:

```php
<?php

return [
    'consignments' => 'Einlieferungen',
    'my_consignments' => 'Meine Einlieferungen',
    'all_consignments' => 'Alle Einlieferungen',
    'describers' => 'Beschreiber',
    'lots' => 'Lose',
    'new_lot' => 'Neues Los',
    'new_consignment' => 'Neue Einlieferung',
    'new_describer' => 'Neuer Beschreiber',
    'save' => 'Speichern',
    'save_and_next' => 'Speichern & Nächstes',
    'save_and_send' => 'Speichern & Zugangsdaten senden',
    'cancel' => 'Abbrechen',
    'create' => 'Anlegen',
    'edit' => 'Bearbeiten',
    'close' => 'Schließen',
    'open' => 'Offen',
    'closed' => 'Geschlossen',
    'status' => 'Status',
    'all_status' => 'Alle Status',
    'all_describers' => 'Alle Beschreiber',
    'consignor_number' => 'Einlieferernummer',
    'internal_nid' => 'Interne NID',
    'start_number' => 'Startnummer (Laufnr.)',
    'catalog_part' => 'Katalogpart',
    'assign_to' => 'Zuweisen an Beschreiber',
    'sequence_number' => 'Lfd.Nr.',
    'category' => 'Kategorie',
    'description' => 'Losbeschreibung',
    'catalog_type' => 'Katalogtyp',
    'catalog_number' => 'Katalognummer',
    'starting_price' => 'Startpreis (€)',
    'notes' => 'Bemerkung',
    'name' => 'Name',
    'email' => 'E-Mail',
    'role' => 'Rolle',
    'password' => 'Passwort',
    'consignment_closed' => 'Einlieferung ist geschlossen.',
    'credentials_sent' => 'Zugangsdaten wurden versendet.',
    'consignment_closed_success' => 'Einlieferung erfolgreich geschlossen.',
    'filter_placeholder' => 'Tippen zum Filtern...',
];
```

`resources/lang/en/messages.php`:

```php
<?php

return [
    'consignments' => 'Consignments',
    'my_consignments' => 'My Consignments',
    'all_consignments' => 'All Consignments',
    'describers' => 'Describers',
    'lots' => 'Lots',
    'new_lot' => 'New Lot',
    'new_consignment' => 'New Consignment',
    'new_describer' => 'New Describer',
    'save' => 'Save',
    'save_and_next' => 'Save & Next',
    'save_and_send' => 'Save & Send Credentials',
    'cancel' => 'Cancel',
    'create' => 'Create',
    'edit' => 'Edit',
    'close' => 'Close',
    'open' => 'Open',
    'closed' => 'Closed',
    'status' => 'Status',
    'all_status' => 'All Status',
    'all_describers' => 'All Describers',
    'consignor_number' => 'Consignor Number',
    'internal_nid' => 'Internal NID',
    'start_number' => 'Start Number',
    'catalog_part' => 'Catalog Part',
    'assign_to' => 'Assign to Describer',
    'sequence_number' => 'Seq.No.',
    'category' => 'Category',
    'description' => 'Lot Description',
    'catalog_type' => 'Catalog Type',
    'catalog_number' => 'Catalog Number',
    'starting_price' => 'Starting Price (€)',
    'notes' => 'Notes',
    'name' => 'Name',
    'email' => 'Email',
    'role' => 'Role',
    'password' => 'Password',
    'consignment_closed' => 'Consignment is closed.',
    'credentials_sent' => 'Credentials have been sent.',
    'consignment_closed_success' => 'Consignment closed successfully.',
    'filter_placeholder' => 'Type to filter...',
];
```

- [ ] **Step 4: Layout-Template erstellen**

`resources/views/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <span class="font-bold text-lg">Externe Beschreiber</span>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
                            {{ __('messages.describers') }}
                        </a>
                        <a href="{{ route('admin.consignments.index') }}" class="text-gray-600 hover:text-gray-900">
                            {{ __('messages.consignments') }}
                        </a>
                    @else
                        <a href="{{ route('describer.consignments.index') }}" class="text-gray-600 hover:text-gray-900">
                            {{ __('messages.my_consignments') }}
                        </a>
                    @endif
                @endauth
            </div>
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('locale.switch', 'de') }}" class="{{ app()->getLocale() === 'de' ? 'font-bold' : '' }}">DE</a>
                    <span>/</span>
                    <a href="{{ route('locale.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'font-bold' : '' }}">EN</a>
                    <span class="text-gray-400">|</span>
                    <span class="text-gray-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900">
                            {{ __('auth.logout') }}
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 rounded px-4 py-3 mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-300 text-red-800 rounded px-4 py-3 mb-4">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    @livewireScripts
</body>
</html>
```

- [ ] **Step 5: Login-View erstellen**

`resources/views/auth/login.blade.php`:

```blade
@extends('layouts.app')

@section('title', __('auth.login'))

@section('content')
<div class="max-w-md mx-auto mt-16">
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">{{ __('auth.login') }}</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('auth.email') }}
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       class="w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror"
                       required autofocus>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('auth.password') }}
                </label>
                <input type="password" name="password" id="password"
                       class="w-full border rounded px-3 py-2 @error('password') border-red-500 @enderror"
                       required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2">
                    <span class="text-sm text-gray-600">{{ __('auth.remember_me') }}</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 text-white rounded py-2 px-4 hover:bg-indigo-700">
                {{ __('auth.login') }}
            </button>
        </form>
    </div>
</div>
@endsection
```

- [ ] **Step 6: LoginController implementieren**

`app/Http/Controllers/Auth/LoginController.php`:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->session()->put('locale', Auth::user()->locale);
            app()->setLocale(Auth::user()->locale);

            if (Auth::user()->isAdmin()) {
                return redirect()->intended('/admin/consignments');
            }

            return redirect()->intended('/consignments');
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', __('auth.password_changed'));
    }
}
```

- [ ] **Step 7: Locale-Switching Middleware in `bootstrap/app.php` hinzufügen**

In `bootstrap/app.php` globale Middleware ergänzen:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\SetLocale::class,
    ]);
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
        'consignment.open' => \App\Http\Middleware\ConsignmentOpenMiddleware::class,
    ]);
})
```

`app/Http/Middleware/SetLocale.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($locale = session('locale')) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
```

- [ ] **Step 8: Routen für Auth registrieren**

In `routes/web.php`:

```php
<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('/password/change', [LoginController::class, 'changePassword'])->middleware('auth')->name('password.change');

// Locale switching
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['de', 'en'])) {
        session(['locale' => $locale]);
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
    }
    return back();
})->name('locale.switch');

// Redirect root to consignments or login
Route::get('/', function () {
    return redirect('/login');
});
```

- [ ] **Step 9: Tests ausführen**

```bash
php artisan test tests/Feature/Auth/LoginTest.php
```

Expected: Alle 7 Tests PASS (einige werden erst nach Task 7 grün, wenn Routen für Beschreiber existieren — der Redirect-Test braucht `/consignments`).

- [ ] **Step 10: Commit**

```bash
git add app/Http/Controllers/Auth/ app/Http/Middleware/SetLocale.php resources/views/ resources/lang/ routes/web.php bootstrap/app.php
git commit -m "feat: Login, Logout, Passwortwechsel, Locale-Switching (ref #TBD)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 7: Beschreiber — Einlieferungsliste & Los-Ansicht

**Files:**
- Create: `app/Http/Controllers/Describer/ConsignmentController.php`
- Create: `app/Http/Controllers/Describer/LotController.php`
- Create: `app/Policies/ConsignmentPolicy.php`
- Create: `resources/views/describer/consignments/index.blade.php`
- Create: `resources/views/describer/consignments/show.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Describer/ConsignmentListTest.php`

- [ ] **Step 1: Tests schreiben**

`tests/Feature/Describer/ConsignmentListTest.php`:

```php
<?php

namespace Tests\Feature\Describer;

use App\Models\Consignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsignmentListTest extends TestCase
{
    use RefreshDatabase;

    public function test_describer_sees_only_own_consignments(): void
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);

        $own = Consignment::factory()->create(['user_id' => $user1->id, 'consignor_number' => '1111111']);
        $other = Consignment::factory()->create(['user_id' => $user2->id, 'consignor_number' => '2222222']);

        $response = $this->actingAs($user1)->get('/consignments');

        $response->assertStatus(200);
        $response->assertSee('1111111');
        $response->assertDontSee('2222222');
    }

    public function test_describer_can_view_own_consignment_detail(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/consignments/{$consignment->id}");

        $response->assertStatus(200);
    }

    public function test_describer_cannot_view_other_users_consignment(): void
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->get("/consignments/{$consignment->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_view_any_consignment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)->get("/consignments/{$consignment->id}");

        $response->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/consignments');

        $response->assertRedirect('/login');
    }
}
```

- [ ] **Step 2: Tests ausführen — sollen fehlschlagen**

```bash
php artisan test tests/Feature/Describer/ConsignmentListTest.php
```

Expected: FAIL.

- [ ] **Step 3: ConsignmentPolicy erstellen**

`app/Policies/ConsignmentPolicy.php`:

```php
<?php

namespace App\Policies;

use App\Models\Consignment;
use App\Models\User;

class ConsignmentPolicy
{
    public function view(User $user, Consignment $consignment): bool
    {
        return $user->isAdmin() || $consignment->user_id === $user->id;
    }

    public function manageLots(User $user, Consignment $consignment): bool
    {
        return ($user->isAdmin() || $consignment->user_id === $user->id)
            && $consignment->isOpen();
    }
}
```

- [ ] **Step 4: Describer ConsignmentController implementieren**

`app/Http/Controllers/Describer/ConsignmentController.php`:

```php
<?php

namespace App\Http\Controllers\Describer;

use App\Http\Controllers\Controller;
use App\Models\Consignment;
use Illuminate\Http\Request;

class ConsignmentController extends Controller
{
    public function index(Request $request)
    {
        $consignments = $request->user()->isAdmin()
            ? Consignment::with(['user', 'catalogPart'])->latest()->get()
            : $request->user()->consignments()->with('catalogPart')->latest()->get();

        return view('describer.consignments.index', compact('consignments'));
    }

    public function show(Request $request, Consignment $consignment)
    {
        $this->authorize('view', $consignment);

        $consignment->load(['lots.category', 'lots.catalogType', 'catalogPart']);

        return view('describer.consignments.show', compact('consignment'));
    }
}
```

- [ ] **Step 5: Beschreiber-Views erstellen**

`resources/views/describer/consignments/index.blade.php`:

```blade
@extends('layouts.app')

@section('title', __('messages.my_consignments'))

@section('content')
<h2 class="text-2xl font-bold mb-4">{{ __('messages.my_consignments') }}</h2>

<div class="grid gap-4">
    @forelse($consignments as $consignment)
        <a href="{{ route('describer.consignments.show', $consignment) }}"
           class="block bg-white rounded-lg shadow p-4 hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <div class="font-bold text-lg">
                        {{ __('messages.consignor_number') }}: {{ $consignment->consignor_number }}
                    </div>
                    <div class="text-gray-500 text-sm">
                        NID: {{ $consignment->internal_nid }}
                        | {{ __('messages.start_number') }}: {{ str_pad($consignment->start_number, 3, '0', STR_PAD_LEFT) }}
                        | {{ $consignment->lots_count ?? $consignment->lots->count() }} {{ __('messages.lots') }}
                    </div>
                    <div class="text-gray-500 text-sm">
                        {{ __('messages.catalog_part') }}: {{ $consignment->catalogPart->name }}
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-sm
                    {{ $consignment->isOpen() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $consignment->isOpen() ? __('messages.open') : __('messages.closed') }}
                </span>
            </div>
        </a>
    @empty
        <p class="text-gray-500">{{ __('messages.no_consignments') ?? 'Keine Einlieferungen vorhanden.' }}</p>
    @endforelse
</div>
@endsection
```

`resources/views/describer/consignments/show.blade.php`:

```blade
@extends('layouts.app')

@section('title', __('messages.consignor_number') . ': ' . $consignment->consignor_number)

@section('content')
<div class="flex justify-between items-center mb-4">
    <div>
        <a href="{{ route('describer.consignments.index') }}" class="text-indigo-600 hover:underline">
            ← {{ __('messages.my_consignments') }}
        </a>
        <h2 class="text-2xl font-bold mt-1">
            {{ __('messages.consignor_number') }}: {{ $consignment->consignor_number }}
        </h2>
        <p class="text-gray-500">
            NID: {{ $consignment->internal_nid }}
            | {{ __('messages.catalog_part') }}: {{ $consignment->catalogPart->name }}
        </p>
    </div>
    <span class="px-3 py-1 rounded-full text-sm
        {{ $consignment->isOpen() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
        {{ $consignment->isOpen() ? __('messages.open') : __('messages.closed') }}
    </span>
</div>

<!-- Lot-Tabelle -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="flex justify-between items-center p-4 border-b">
        <h3 class="font-bold">{{ __('messages.lots') }} ({{ $consignment->lots->count() }})</h3>
        @if($consignment->isOpen())
            <button onclick="document.getElementById('lot-form').classList.toggle('hidden')"
                    class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 text-sm">
                + {{ __('messages.new_lot') }}
            </button>
        @endif
    </div>

    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-2">{{ __('messages.sequence_number') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.category') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.description') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.catalog_type') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.catalog_number') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.starting_price') }}</th>
                @if($consignment->isOpen())
                    <th class="px-4 py-2"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($consignment->lots->sortBy('sequence_number') as $lot)
                <tr class="border-t">
                    <td class="px-4 py-2 font-mono text-amber-600">
                        {{ str_pad($lot->sequence_number, 3, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-4 py-2">{{ $lot->category->name }}</td>
                    <td class="px-4 py-2 max-w-xs truncate">{{ $lot->description }}</td>
                    <td class="px-4 py-2">{{ $lot->catalogType->name }}</td>
                    <td class="px-4 py-2">{{ $lot->catalog_number }}</td>
                    <td class="px-4 py-2">{{ number_format($lot->starting_price, 2, ',', '.') }} €</td>
                    @if($consignment->isOpen())
                        <td class="px-4 py-2">
                            <a href="{{ route('describer.lots.edit', [$consignment, $lot]) }}"
                               class="text-indigo-600 hover:underline">✏️</a>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($consignment->isOpen())
    <div id="lot-form" class="mt-4 hidden">
        @livewire('lot-form', ['consignment' => $consignment])
    </div>
@endif
@endsection
```

- [ ] **Step 6: Routen für Beschreiber in `routes/web.php` ergänzen**

Am Ende von `routes/web.php` hinzufügen:

```php
use App\Http\Controllers\Describer\ConsignmentController as DescriberConsignmentController;
use App\Http\Controllers\Describer\LotController as DescriberLotController;

// Describer routes
Route::middleware('auth')->group(function () {
    Route::get('/consignments', [DescriberConsignmentController::class, 'index'])
        ->name('describer.consignments.index');
    Route::get('/consignments/{consignment}', [DescriberConsignmentController::class, 'show'])
        ->name('describer.consignments.show');
});
```

- [ ] **Step 7: Tests ausführen**

```bash
php artisan test tests/Feature/Describer/ConsignmentListTest.php
```

Expected: Alle 5 Tests PASS.

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/Describer/ app/Policies/ resources/views/describer/ routes/web.php
git commit -m "feat: Beschreiber-Ansicht für Einlieferungen und Lose (ref #TBD)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 8: Beschreiber — Los-Erfassung (Livewire)

**Files:**
- Create: `app/Livewire/LotForm.php`
- Create: `resources/views/livewire/lot-form.blade.php`
- Create: `app/Http/Controllers/Describer/LotController.php`
- Create: `app/Http/Requests/StoreLotRequest.php`
- Create: `app/Http/Requests/UpdateLotRequest.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Describer/LotManagementTest.php`

- [ ] **Step 1: Tests für Los-CRUD schreiben**

`tests/Feature/Describer/LotManagementTest.php`:

```php
<?php

namespace Tests\Feature\Describer;

use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Consignment;
use App\Models\Lot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LotManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createLotData(): array
    {
        return [
            'category_id' => Category::factory()->create()->id,
            'description' => 'Dt. Reich Mi.Nr. 1-3 gestempelt',
            'catalog_type_id' => CatalogType::factory()->create()->id,
            'catalog_number' => '1-3',
            'starting_price' => 150.00,
            'notes' => 'Gute Erhaltung',
        ];
    }

    public function test_describer_can_create_lot(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create([
            'user_id' => $user->id,
            'start_number' => 1,
            'next_number' => 1,
        ]);

        $response = $this->actingAs($user)->post(
            "/consignments/{$consignment->id}/lots",
            $this->createLotData()
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('lots', [
            'consignment_id' => $consignment->id,
            'sequence_number' => 1,
            'catalog_number' => '1-3',
        ]);
        $this->assertEquals(2, $consignment->fresh()->next_number);
    }

    public function test_sequence_number_auto_increments(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create([
            'user_id' => $user->id,
            'start_number' => 5,
            'next_number' => 5,
        ]);

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

        $response = $this->actingAs($user)->put(
            "/consignments/{$consignment->id}/lots/{$lot->id}",
            array_merge($this->createLotData(), ['description' => 'Updated description'])
        );

        $response->assertRedirect();
        $this->assertEquals('Updated description', $lot->fresh()->description);
    }

    public function test_describer_cannot_create_lot_on_closed_consignment(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create([
            'user_id' => $user->id,
            'status' => 'closed',
        ]);

        $response = $this->actingAs($user)->post(
            "/consignments/{$consignment->id}/lots",
            $this->createLotData()
        );

        $response->assertStatus(403);
    }

    public function test_describer_cannot_modify_lot_on_other_users_consignment(): void
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user2->id]);
        $lot = Lot::factory()->create(['consignment_id' => $consignment->id]);

        $response = $this->actingAs($user1)->put(
            "/consignments/{$consignment->id}/lots/{$lot->id}",
            $this->createLotData()
        );

        $response->assertStatus(403);
    }

    public function test_describer_can_delete_lot(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $consignment = Consignment::factory()->create(['user_id' => $user->id]);
        $lot = Lot::factory()->create(['consignment_id' => $consignment->id]);

        $response = $this->actingAs($user)->delete(
            "/consignments/{$consignment->id}/lots/{$lot->id}"
        );

        $response->assertRedirect();
        $this->assertDatabaseMissing('lots', ['id' => $lot->id]);
    }
}
```

- [ ] **Step 2: Tests ausführen — sollen fehlschlagen**

```bash
php artisan test tests/Feature/Describer/LotManagementTest.php
```

Expected: FAIL.

- [ ] **Step 3: Form Requests erstellen**

`app/Http/Requests/StoreLotRequest.php`:

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
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'catalog_type_id' => ['required', 'exists:catalog_types,id'],
            'catalog_number' => ['required', 'string', 'max:255'],
            'starting_price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
```

`app/Http/Requests/UpdateLotRequest.php`:

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
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'catalog_type_id' => ['required', 'exists:catalog_types,id'],
            'catalog_number' => ['required', 'string', 'max:255'],
            'starting_price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
```

- [ ] **Step 4: LotController implementieren**

`app/Http/Controllers/Describer/LotController.php`:

```php
<?php

namespace App\Http\Controllers\Describer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLotRequest;
use App\Http\Requests\UpdateLotRequest;
use App\Models\Consignment;
use App\Models\Lot;
use Illuminate\Support\Facades\DB;

class LotController extends Controller
{
    public function store(StoreLotRequest $request, Consignment $consignment)
    {
        DB::transaction(function () use ($request, $consignment) {
            $consignment->lots()->create(
                array_merge($request->validated(), [
                    'sequence_number' => $consignment->next_number,
                ])
            );

            $consignment->increment('next_number');
        });

        return redirect()
            ->route('describer.consignments.show', $consignment)
            ->with('success', __('messages.lot_created'));
    }

    public function edit(Consignment $consignment, Lot $lot)
    {
        $this->authorize('manageLots', $consignment);

        $lot->load(['category', 'catalogType']);

        return view('describer.lots.edit', compact('consignment', 'lot'));
    }

    public function update(UpdateLotRequest $request, Consignment $consignment, Lot $lot)
    {
        $lot->update($request->validated());

        return redirect()
            ->route('describer.consignments.show', $consignment)
            ->with('success', __('messages.lot_updated'));
    }

    public function destroy(Consignment $consignment, Lot $lot)
    {
        $this->authorize('manageLots', $consignment);

        $lot->delete();

        return redirect()
            ->route('describer.consignments.show', $consignment)
            ->with('success', __('messages.lot_deleted'));
    }
}
```

- [ ] **Step 5: Livewire LotForm-Komponente erstellen**

`app/Livewire/LotForm.php`:

```php
<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\CatalogType;
use App\Models\Consignment;
use Livewire\Component;

class LotForm extends Component
{
    public Consignment $consignment;

    public string $categorySearch = '';
    public string $catalogTypeSearch = '';
    public ?int $category_id = null;
    public ?int $catalog_type_id = null;
    public string $catalog_number = '';
    public string $starting_price = '';
    public string $description = '';
    public string $notes = '';

    public bool $showCategoryDropdown = false;
    public bool $showCatalogTypeDropdown = false;

    public function updatedCategorySearch(): void
    {
        $this->showCategoryDropdown = strlen($this->categorySearch) > 0;
        $this->category_id = null;
    }

    public function updatedCatalogTypeSearch(): void
    {
        $this->showCatalogTypeDropdown = strlen($this->catalogTypeSearch) > 0;
        $this->catalog_type_id = null;
    }

    public function selectCategory(int $id, string $name): void
    {
        $this->category_id = $id;
        $this->categorySearch = $name;
        $this->showCategoryDropdown = false;
    }

    public function selectCatalogType(int $id, string $name): void
    {
        $this->catalog_type_id = $id;
        $this->catalogTypeSearch = $name;
        $this->showCatalogTypeDropdown = false;
    }

    public function getFilteredCategoriesProperty()
    {
        $locale = app()->getLocale();
        $field = "name_{$locale}";

        return Category::where($field, 'like', "%{$this->categorySearch}%")
            ->limit(10)
            ->get();
    }

    public function getFilteredCatalogTypesProperty()
    {
        $locale = app()->getLocale();
        $field = "name_{$locale}";

        return CatalogType::where($field, 'like', "%{$this->catalogTypeSearch}%")
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.lot-form');
    }
}
```

`resources/views/livewire/lot-form.blade.php`:

```blade
<div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
    <div class="font-bold text-indigo-800 mb-3">
        {{ __('messages.new_lot') }} — {{ __('messages.sequence_number') }}
        {{ str_pad($consignment->next_number, 3, '0', STR_PAD_LEFT) }}
    </div>

    <form method="POST" action="{{ route('describer.lots.store', $consignment) }}">
        @csrf

        {{-- Zeile 1: Kategorie (volle Breite, filter-as-you-type) --}}
        <div class="mb-3 relative">
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.category') }}</label>
            <input type="text"
                   wire:model.live.debounce.200ms="categorySearch"
                   wire:focus="$set('showCategoryDropdown', true)"
                   placeholder="{{ __('messages.filter_placeholder') }}"
                   class="w-full border rounded px-3 py-2 @error('category_id') border-red-500 @enderror"
                   autocomplete="off">
            <input type="hidden" name="category_id" value="{{ $category_id }}">

            @if($showCategoryDropdown && $this->filteredCategories->count())
                <div class="absolute z-10 w-full bg-white border rounded-b shadow-lg mt-0">
                    @foreach($this->filteredCategories as $cat)
                        <div wire:click="selectCategory({{ $cat->id }}, '{{ $cat->name }}')"
                             class="px-3 py-2 hover:bg-indigo-100 cursor-pointer">
                            {{ $cat->name }}
                        </div>
                    @endforeach
                </div>
            @endif
            @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Zeile 2: Katalogtyp, Katalognummer, Startpreis --}}
        <div class="grid grid-cols-3 gap-3 mb-3">
            <div class="relative">
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.catalog_type') }}</label>
                <input type="text"
                       wire:model.live.debounce.200ms="catalogTypeSearch"
                       wire:focus="$set('showCatalogTypeDropdown', true)"
                       placeholder="{{ __('messages.filter_placeholder') }}"
                       class="w-full border rounded px-3 py-2"
                       autocomplete="off">
                <input type="hidden" name="catalog_type_id" value="{{ $catalog_type_id }}">

                @if($showCatalogTypeDropdown && $this->filteredCatalogTypes->count())
                    <div class="absolute z-10 w-full bg-white border rounded-b shadow-lg mt-0">
                        @foreach($this->filteredCatalogTypes as $ct)
                            <div wire:click="selectCatalogType({{ $ct->id }}, '{{ $ct->name }}')"
                                 class="px-3 py-2 hover:bg-indigo-100 cursor-pointer">
                                {{ $ct->name }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.catalog_number') }}</label>
                <input type="text" name="catalog_number" wire:model="catalog_number"
                       placeholder="z.B. 438" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.starting_price') }}</label>
                <input type="number" name="starting_price" wire:model="starting_price"
                       step="0.01" min="0" placeholder="0,00"
                       class="w-full border rounded px-3 py-2">
            </div>
        </div>

        {{-- Zeile 3: Losbeschreibung (Textarea, 4 Zeilen) --}}
        <div class="mb-3">
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.description') }}</label>
            <textarea name="description" wire:model="description" rows="4"
                      class="w-full border rounded px-3 py-2 resize-y"
                      placeholder="{{ __('messages.description') }}..."></textarea>
        </div>

        {{-- Zeile 4: Bemerkung (einzeilig) --}}
        <div class="mb-3">
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.notes') }}</label>
            <input type="text" name="notes" wire:model="notes"
                   class="w-full border rounded px-3 py-2"
                   placeholder="{{ __('messages.notes') }} (optional)">
        </div>

        <div class="flex justify-end gap-2">
            <button type="button"
                    onclick="document.getElementById('lot-form').classList.add('hidden')"
                    class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">
                {{ __('messages.cancel') }}
            </button>
            <button type="submit"
                    class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                {{ __('messages.save_and_next') }}
            </button>
        </div>
    </form>
</div>
```

- [ ] **Step 6: Lot-Routen in `routes/web.php` ergänzen**

```php
// Inside the auth middleware group:
Route::post('/consignments/{consignment}/lots', [DescriberLotController::class, 'store'])
    ->name('describer.lots.store');
Route::get('/consignments/{consignment}/lots/{lot}/edit', [DescriberLotController::class, 'edit'])
    ->name('describer.lots.edit');
Route::put('/consignments/{consignment}/lots/{lot}', [DescriberLotController::class, 'update'])
    ->name('describer.lots.update');
Route::delete('/consignments/{consignment}/lots/{lot}', [DescriberLotController::class, 'destroy'])
    ->name('describer.lots.destroy');
```

- [ ] **Step 7: Tests ausführen**

```bash
php artisan test tests/Feature/Describer/LotManagementTest.php
```

Expected: Alle 6 Tests PASS.

- [ ] **Step 8: Commit**

```bash
git add app/Livewire/ app/Http/Controllers/Describer/ app/Http/Requests/ resources/views/ routes/web.php
git commit -m "feat: Los-Erfassung mit Livewire Filter-as-you-type (ref #TBD)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 9: Admin — Beschreiberverwaltung

**Files:**
- Create: `app/Http/Controllers/Admin/UserController.php`
- Create: `app/Http/Requests/StoreUserRequest.php`
- Create: `app/Mail/CredentialsMail.php`
- Create: `resources/views/admin/users/index.blade.php`
- Create: `resources/views/emails/credentials.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Admin/UserManagementTest.php`
- Test: `tests/Unit/Mail/CredentialsMailTest.php`

- [ ] **Step 1: Tests für Beschreiberverwaltung schreiben**

`tests/Feature/Admin/UserManagementTest.php`:

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Mail\CredentialsMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_user_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(3)->create(['role' => 'user']);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_describer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Hans Schmidt',
            'email' => 'hans@example.com',
            'password' => 'securepass123',
            'role' => 'user',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'hans@example.com',
            'role' => 'user',
        ]);
    }

    public function test_admin_can_create_and_send_credentials(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Hans Schmidt',
            'email' => 'hans@example.com',
            'password' => 'securepass123',
            'role' => 'user',
            'send_credentials' => '1',
        ]);

        $response->assertRedirect();
        Mail::assertSent(CredentialsMail::class, function ($mail) {
            return $mail->hasTo('hans@example.com');
        });
    }

    public function test_admin_can_resend_credentials(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($admin)->post("/admin/users/{$user->id}/send-credentials");

        $response->assertRedirect();
        Mail::assertSent(CredentialsMail::class);
    }

    public function test_admin_can_update_describer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user', 'name' => 'Old Name']);

        $response = $this->actingAs($admin)->put("/admin/users/{$user->id}", [
            'name' => 'New Name',
            'email' => $user->email,
            'role' => 'user',
        ]);

        $response->assertRedirect();
        $this->assertEquals('New Name', $user->fresh()->name);
    }

    public function test_describer_cannot_access_user_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertStatus(403);
    }
}
```

- [ ] **Step 2: Test für CredentialsMail schreiben**

`tests/Unit/Mail/CredentialsMailTest.php`:

```php
<?php

namespace Tests\Unit\Mail;

use App\Mail\CredentialsMail;
use App\Models\User;
use Tests\TestCase;

class CredentialsMailTest extends TestCase
{
    public function test_mail_contains_credentials(): void
    {
        $user = User::factory()->make([
            'name' => 'Hans Schmidt',
            'email' => 'hans@example.com',
        ]);

        $mail = new CredentialsMail($user, 'testpassword123', 'http://localhost/login');

        $mail->assertSeeInHtml('Hans Schmidt');
        $mail->assertSeeInHtml('hans@example.com');
        $mail->assertSeeInHtml('testpassword123');
        $mail->assertSeeInHtml('http://localhost/login');
    }
}
```

- [ ] **Step 3: Tests ausführen — sollen fehlschlagen**

```bash
php artisan test tests/Feature/Admin/UserManagementTest.php tests/Unit/Mail/CredentialsMailTest.php
```

Expected: FAIL.

- [ ] **Step 4: CredentialsMail erstellen**

`app/Mail/CredentialsMail.php`:

```php
<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $plainPassword,
        public string $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('messages.credentials_mail_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.credentials',
        );
    }
}
```

`resources/views/emails/credentials.blade.php`:

```blade
<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; line-height: 1.6;">
    <h2>{{ __('messages.credentials_mail_greeting', ['name' => $user->name]) }}</h2>

    <p>{{ __('messages.credentials_mail_intro') }}</p>

    <table style="border-collapse: collapse; margin: 16px 0;">
        <tr>
            <td style="padding: 8px; font-weight: bold;">{{ __('messages.email') }}:</td>
            <td style="padding: 8px;">{{ $user->email }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">{{ __('auth.password') }}:</td>
            <td style="padding: 8px; font-family: monospace;">{{ $plainPassword }}</td>
        </tr>
    </table>

    <p>
        <a href="{{ $loginUrl }}" style="background: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
            {{ __('auth.login') }}
        </a>
    </p>

    <p style="color: #666; font-size: 14px; margin-top: 24px;">
        {{ __('messages.credentials_mail_footer') }}
    </p>
</body>
</html>
```

Ergänze die Sprachdateien `resources/lang/de/messages.php` und `resources/lang/en/messages.php` um:

```php
// de
'credentials_mail_subject' => 'Ihre Zugangsdaten - Externe Beschreiber',
'credentials_mail_greeting' => 'Hallo :name,',
'credentials_mail_intro' => 'Sie haben Zugangsdaten für die Externe-Beschreiber-Plattform erhalten.',
'credentials_mail_footer' => 'Bitte ändern Sie Ihr Passwort nach dem ersten Login.',
'lot_created' => 'Los erfolgreich angelegt.',
'lot_updated' => 'Los erfolgreich aktualisiert.',
'lot_deleted' => 'Los erfolgreich gelöscht.',
'user_created' => 'Beschreiber erfolgreich angelegt.',
'user_updated' => 'Beschreiber erfolgreich aktualisiert.',

// en
'credentials_mail_subject' => 'Your Credentials - External Describers',
'credentials_mail_greeting' => 'Hello :name,',
'credentials_mail_intro' => 'You have been given access to the External Describers platform.',
'credentials_mail_footer' => 'Please change your password after your first login.',
'lot_created' => 'Lot created successfully.',
'lot_updated' => 'Lot updated successfully.',
'lot_deleted' => 'Lot deleted successfully.',
'user_created' => 'Describer created successfully.',
'user_updated' => 'Describer updated successfully.',
```

- [ ] **Step 5: StoreUserRequest erstellen**

`app/Http/Requests/StoreUserRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email' . ($userId ? ",{$userId}" : '')],
            'role' => ['required', 'in:admin,user'],
        ];

        if (!$userId) {
            $rules['password'] = ['required', Password::min(8)];
        } else {
            $rules['password'] = ['nullable', Password::min(8)];
        }

        return $rules;
    }
}
```

- [ ] **Step 6: Admin UserController implementieren**

`app/Http/Controllers/Admin/UserController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Mail\CredentialsMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('consignments')->orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function store(StoreUserRequest $request)
    {
        $plainPassword = $request->password;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($plainPassword),
            'role' => $request->role,
            'locale' => 'de',
        ]);

        if ($request->boolean('send_credentials')) {
            Mail::to($user)->send(
                new CredentialsMail($user, $plainPassword, route('login'))
            );
        }

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.user_created'));
    }

    public function update(StoreUserRequest $request, User $user)
    {
        $data = $request->only(['name', 'email', 'role']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.user_updated'));
    }

    public function sendCredentials(User $user)
    {
        $plainPassword = Str::random(10);
        $user->update(['password' => Hash::make($plainPassword)]);

        Mail::to($user)->send(
            new CredentialsMail($user, $plainPassword, route('login'))
        );

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.credentials_sent'));
    }
}
```

- [ ] **Step 7: Admin Users View erstellen**

`resources/views/admin/users/index.blade.php`:

```blade
@extends('layouts.app')

@section('title', __('messages.describers'))

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-bold">{{ __('messages.describers') }} ({{ $users->count() }})</h2>
    <button onclick="document.getElementById('user-form').classList.toggle('hidden')"
            class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 text-sm">
        + {{ __('messages.new_describer') }}
    </button>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-2">{{ __('messages.name') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.email') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.role') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.consignments') }}</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2 text-gray-500">{{ $user->email }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded-full text-xs
                            {{ $user->isAdmin() ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800' }}">
                            {{ $user->isAdmin() ? 'Admin' : __('messages.describers') }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ $user->consignments_count }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        <button onclick="editUser({{ $user->id }})" class="text-indigo-600">✏️</button>
                        @if(!$user->isAdmin())
                            <form method="POST" action="{{ route('admin.users.send-credentials', $user) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-amber-600"
                                        onclick="return confirm('{{ __('messages.confirm_send_credentials') ?? 'Zugangsdaten senden?' }}')">
                                    📧
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="user-form" class="hidden mt-4 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
    <div class="font-bold text-indigo-800 mb-3">{{ __('messages.new_describer') }}</div>
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.name') }}</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.email') }}</label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('auth.password') }}</label>
                <input type="text" name="password" class="w-full border rounded px-3 py-2"
                       value="{{ Str::random(10) }}" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.role') }}</label>
                <select name="role" class="w-full border rounded px-3 py-2">
                    <option value="user">{{ __('messages.describers') }}</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-3">
            <button type="button"
                    onclick="document.getElementById('user-form').classList.add('hidden')"
                    class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">
                {{ __('messages.cancel') }}
            </button>
            <button type="submit" name="send_credentials" value="0"
                    class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                {{ __('messages.save') }}
            </button>
            <button type="submit" name="send_credentials" value="1"
                    class="px-4 py-2 bg-amber-700 text-white rounded hover:bg-amber-800">
                {{ __('messages.save_and_send') }} 📧
            </button>
        </div>
    </form>
</div>
@endsection
```

- [ ] **Step 8: Admin-Routen in `routes/web.php` ergänzen**

```php
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ConsignmentController as AdminConsignmentController;

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::post('/users/{user}/send-credentials', [AdminUserController::class, 'sendCredentials'])
        ->name('admin.users.send-credentials');
});
```

- [ ] **Step 9: Tests ausführen**

```bash
php artisan test tests/Feature/Admin/UserManagementTest.php tests/Unit/Mail/CredentialsMailTest.php
```

Expected: Alle 7 Tests PASS.

- [ ] **Step 10: Commit**

```bash
git add app/Http/Controllers/Admin/UserController.php app/Http/Requests/StoreUserRequest.php app/Mail/ resources/views/admin/ resources/views/emails/ resources/lang/ routes/web.php
git commit -m "feat: Admin Beschreiberverwaltung mit E-Mail-Versand (ref #TBD)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 10: Admin — Einlieferungsverwaltung

**Files:**
- Create: `app/Http/Controllers/Admin/ConsignmentController.php`
- Create: `app/Http/Requests/StoreConsignmentRequest.php`
- Create: `resources/views/admin/consignments/index.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Admin/ConsignmentManagementTest.php`

- [ ] **Step 1: Tests schreiben**

`tests/Feature/Admin/ConsignmentManagementTest.php`:

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\CatalogPart;
use App\Models\Consignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsignmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_all_consignments(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Consignment::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/consignments');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_consignment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $describer = User::factory()->create(['role' => 'user']);
        $catalogPart = CatalogPart::factory()->create(['is_default' => true]);

        $response = $this->actingAs($admin)->post('/admin/consignments', [
            'consignor_number' => '7389123',
            'internal_nid' => '4521',
            'start_number' => 1,
            'catalog_part_id' => $catalogPart->id,
            'user_id' => $describer->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('consignments', [
            'consignor_number' => '7389123',
            'internal_nid' => '4521',
            'start_number' => 1,
            'next_number' => 1,
            'user_id' => $describer->id,
            'status' => 'open',
        ]);
    }

    public function test_admin_can_close_consignment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $consignment = Consignment::factory()->create(['status' => 'open']);

        $response = $this->actingAs($admin)->post("/admin/consignments/{$consignment->id}/close");

        $response->assertRedirect();
        $this->assertEquals('closed', $consignment->fresh()->status);
    }

    public function test_admin_can_filter_by_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Consignment::factory()->create(['status' => 'open', 'consignor_number' => '1111111']);
        Consignment::factory()->create(['status' => 'closed', 'consignor_number' => '2222222']);

        $response = $this->actingAs($admin)->get('/admin/consignments?status=open');

        $response->assertStatus(200);
        $response->assertSee('1111111');
        $response->assertDontSee('2222222');
    }

    public function test_admin_can_filter_by_describer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        Consignment::factory()->create(['user_id' => $user1->id, 'consignor_number' => '1111111']);
        Consignment::factory()->create(['user_id' => $user2->id, 'consignor_number' => '2222222']);

        $response = $this->actingAs($admin)->get("/admin/consignments?user_id={$user1->id}");

        $response->assertStatus(200);
        $response->assertSee('1111111');
        $response->assertDontSee('2222222');
    }

    public function test_describer_cannot_access_admin_consignments(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/consignments');

        $response->assertStatus(403);
    }
}
```

- [ ] **Step 2: Tests ausführen — sollen fehlschlagen**

```bash
php artisan test tests/Feature/Admin/ConsignmentManagementTest.php
```

Expected: FAIL.

- [ ] **Step 3: StoreConsignmentRequest erstellen**

`app/Http/Requests/StoreConsignmentRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'consignor_number' => ['required', 'string', 'max:255'],
            'internal_nid' => ['required', 'string', 'max:255'],
            'start_number' => ['required', 'integer', 'min:1'],
            'catalog_part_id' => ['required', 'exists:catalog_parts,id'],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }
}
```

- [ ] **Step 4: Admin ConsignmentController implementieren**

`app/Http/Controllers/Admin/ConsignmentController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsignmentRequest;
use App\Models\CatalogPart;
use App\Models\Consignment;
use App\Models\User;
use Illuminate\Http\Request;

class ConsignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Consignment::with(['user', 'catalogPart'])->withCount('lots');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $consignments = $query->latest()->get();
        $describers = User::where('role', 'user')->orderBy('name')->get();
        $catalogParts = CatalogPart::all();
        $defaultCatalogPart = CatalogPart::where('is_default', true)->first();

        return view('admin.consignments.index', compact(
            'consignments', 'describers', 'catalogParts', 'defaultCatalogPart'
        ));
    }

    public function store(StoreConsignmentRequest $request)
    {
        Consignment::create([
            'consignor_number' => $request->consignor_number,
            'internal_nid' => $request->internal_nid,
            'start_number' => $request->start_number,
            'next_number' => $request->start_number,
            'catalog_part_id' => $request->catalog_part_id,
            'user_id' => $request->user_id,
            'status' => 'open',
        ]);

        return redirect()->route('admin.consignments.index')
            ->with('success', __('messages.consignment_created'));
    }

    public function close(Consignment $consignment)
    {
        $consignment->update(['status' => 'closed']);

        return redirect()->route('admin.consignments.index')
            ->with('success', __('messages.consignment_closed_success'));
    }
}
```

- [ ] **Step 5: Admin Consignments View erstellen**

`resources/views/admin/consignments/index.blade.php`:

```blade
@extends('layouts.app')

@section('title', __('messages.all_consignments'))

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-bold">{{ __('messages.all_consignments') }} ({{ $consignments->count() }})</h2>
    <button onclick="document.getElementById('consignment-form').classList.toggle('hidden')"
            class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 text-sm">
        + {{ __('messages.new_consignment') }}
    </button>
</div>

<!-- Filter -->
<div class="flex gap-3 mb-4">
    <form method="GET" class="flex gap-3">
        <select name="status" onchange="this.form.submit()" class="border rounded px-3 py-2 text-sm">
            <option value="">{{ __('messages.all_status') }}</option>
            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>{{ __('messages.open') }}</option>
            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>{{ __('messages.closed') }}</option>
        </select>
        <select name="user_id" onchange="this.form.submit()" class="border rounded px-3 py-2 text-sm">
            <option value="">{{ __('messages.all_describers') }}</option>
            @foreach($describers as $describer)
                <option value="{{ $describer->id }}" {{ request('user_id') == $describer->id ? 'selected' : '' }}>
                    {{ $describer->name }}
                </option>
            @endforeach
        </select>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-2">{{ __('messages.consignor_number') }}</th>
                <th class="text-left px-4 py-2">NID</th>
                <th class="text-left px-4 py-2">{{ __('messages.describers') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.lots') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.status') }}</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($consignments as $consignment)
                <tr class="border-t">
                    <td class="px-4 py-2 font-bold">{{ $consignment->consignor_number }}</td>
                    <td class="px-4 py-2 text-gray-500">{{ $consignment->internal_nid }}</td>
                    <td class="px-4 py-2">{{ $consignment->user->name }}</td>
                    <td class="px-4 py-2">{{ $consignment->lots_count }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded-full text-xs
                            {{ $consignment->isOpen() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $consignment->isOpen() ? __('messages.open') : __('messages.closed') }}
                        </span>
                    </td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ route('describer.consignments.show', $consignment) }}"
                           class="text-indigo-600">👁️</a>
                        @if($consignment->isOpen())
                            <form method="POST" action="{{ route('admin.consignments.close', $consignment) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600"
                                        onclick="return confirm('{{ __('messages.confirm_close') ?? 'Einlieferung schließen?' }}')">
                                    🔒
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="consignment-form" class="hidden mt-4 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
    <div class="font-bold text-indigo-800 mb-3">{{ __('messages.new_consignment') }}</div>
    <form method="POST" action="{{ route('admin.consignments.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.consignor_number') }}</label>
                <input type="text" name="consignor_number" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.internal_nid') }}</label>
                <input type="text" name="internal_nid" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.start_number') }}</label>
                <input type="number" name="start_number" value="1" min="1"
                       class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.catalog_part') }}</label>
                <select name="catalog_part_id" class="w-full border rounded px-3 py-2">
                    @foreach($catalogParts as $part)
                        <option value="{{ $part->id }}" {{ $part->is_default ? 'selected' : '' }}>
                            {{ $part->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.assign_to') }}</label>
                <select name="user_id" class="w-full border rounded px-3 py-2" required>
                    @foreach($describers as $describer)
                        <option value="{{ $describer->id }}">{{ $describer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-3">
            <button type="button"
                    onclick="document.getElementById('consignment-form').classList.add('hidden')"
                    class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">
                {{ __('messages.cancel') }}
            </button>
            <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                {{ __('messages.create') }}
            </button>
        </div>
    </form>
</div>
@endsection
```

Ergänze in den Sprachdateien:

```php
// de/messages.php
'consignment_created' => 'Einlieferung erfolgreich angelegt.',

// en/messages.php
'consignment_created' => 'Consignment created successfully.',
```

- [ ] **Step 6: Admin-Consignment-Routen in `routes/web.php` ergänzen**

In der admin-Middleware-Gruppe ergänzen:

```php
Route::get('/consignments', [AdminConsignmentController::class, 'index'])->name('admin.consignments.index');
Route::post('/consignments', [AdminConsignmentController::class, 'store'])->name('admin.consignments.store');
Route::post('/consignments/{consignment}/close', [AdminConsignmentController::class, 'close'])
    ->name('admin.consignments.close');
```

- [ ] **Step 7: Tests ausführen**

```bash
php artisan test tests/Feature/Admin/ConsignmentManagementTest.php
```

Expected: Alle 6 Tests PASS.

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/Admin/ConsignmentController.php app/Http/Requests/StoreConsignmentRequest.php resources/views/admin/consignments/ resources/lang/ routes/web.php
git commit -m "feat: Admin Einlieferungsverwaltung mit Filter und Schließen (ref #TBD)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 11: REST API

**Files:**
- Create: `app/Http/Controllers/Api/ConsignmentController.php`
- Create: `app/Http/Controllers/Api/LotController.php`
- Create: `app/Http/Controllers/Api/UserController.php`
- Modify: `routes/api.php`
- Test: `tests/Feature/Api/ConsignmentApiTest.php`
- Test: `tests/Feature/Api/LotApiTest.php`
- Test: `tests/Feature/Api/UserApiTest.php`

- [ ] **Step 1: API-Tests schreiben**

`tests/Feature/Api/ConsignmentApiTest.php`:

```php
<?php

namespace Tests\Feature\Api;

use App\Models\CatalogPart;
use App\Models\Consignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsignmentApiTest extends TestCase
{
    use RefreshDatabase;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.api.key' => 'test-api-key']);
        $this->headers = ['X-API-Key' => 'test-api-key'];
    }

    public function test_list_consignments(): void
    {
        Consignment::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/consignments', $this->headers);

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_filter_consignments_by_status(): void
    {
        Consignment::factory()->create(['status' => 'open']);
        Consignment::factory()->create(['status' => 'closed']);

        $response = $this->getJson('/api/v1/consignments?status=open', $this->headers);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_show_consignment(): void
    {
        $consignment = Consignment::factory()->create(['consignor_number' => '7389123']);

        $response = $this->getJson("/api/v1/consignments/{$consignment->id}", $this->headers);

        $response->assertStatus(200);
        $response->assertJsonPath('data.consignor_number', '7389123');
    }

    public function test_create_consignment(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $catalogPart = CatalogPart::factory()->create();

        $response = $this->postJson('/api/v1/consignments', [
            'consignor_number' => '7389999',
            'internal_nid' => '9999',
            'start_number' => 1,
            'catalog_part_id' => $catalogPart->id,
            'user_id' => $user->id,
        ], $this->headers);

        $response->assertStatus(201);
        $response->assertJsonPath('data.consignor_number', '7389999');
    }

    public function test_update_consignment(): void
    {
        $consignment = Consignment::factory()->create();

        $response = $this->putJson("/api/v1/consignments/{$consignment->id}", [
            'consignor_number' => '0000000',
        ], $this->headers);

        $response->assertStatus(200);
        $this->assertEquals('0000000', $consignment->fresh()->consignor_number);
    }

    public function test_requires_api_key(): void
    {
        $response = $this->getJson('/api/v1/consignments');

        $response->assertStatus(401);
    }
}
```

`tests/Feature/Api/LotApiTest.php`:

```php
<?php

namespace Tests\Feature\Api;

use App\Models\Consignment;
use App\Models\Lot;
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
        Lot::factory()->count(5)->create(['consignment_id' => $consignment->id]);

        $response = $this->getJson("/api/v1/consignments/{$consignment->id}/lots", $this->headers);

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
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
}
```

`tests/Feature/Api/UserApiTest.php`:

```php
<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.api.key' => 'test-api-key']);
        $this->headers = ['X-API-Key' => 'test-api-key'];
    }

    public function test_create_user(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => 'API User',
            'email' => 'api@example.com',
            'password' => 'securepass123',
            'role' => 'user',
        ], $this->headers);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'api@example.com']);
    }

    public function test_update_user(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $response = $this->putJson("/api/v1/users/{$user->id}", [
            'name' => 'New Name',
        ], $this->headers);

        $response->assertStatus(200);
        $this->assertEquals('New Name', $user->fresh()->name);
    }
}
```

- [ ] **Step 2: Tests ausführen — sollen fehlschlagen**

```bash
php artisan test tests/Feature/Api/
```

Expected: FAIL.

- [ ] **Step 3: API ConsignmentController implementieren**

`app/Http/Controllers/Api/ConsignmentController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consignment;
use Illuminate\Http\Request;

class ConsignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Consignment::with(['user', 'catalogPart'])->withCount('lots');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('consignor_number')) {
            $query->where('consignor_number', $request->consignor_number);
        }

        return response()->json(['data' => $query->latest()->get()]);
    }

    public function show(Consignment $consignment)
    {
        $consignment->load(['user', 'catalogPart', 'lots.category', 'lots.catalogType']);

        return response()->json(['data' => $consignment]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'consignor_number' => ['required', 'string'],
            'internal_nid' => ['required', 'string'],
            'start_number' => ['required', 'integer', 'min:1'],
            'catalog_part_id' => ['required', 'exists:catalog_parts,id'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $consignment = Consignment::create(array_merge($validated, [
            'next_number' => $validated['start_number'],
            'status' => 'open',
        ]));

        return response()->json(['data' => $consignment], 201);
    }

    public function update(Request $request, Consignment $consignment)
    {
        $validated = $request->validate([
            'consignor_number' => ['sometimes', 'string'],
            'internal_nid' => ['sometimes', 'string'],
            'catalog_part_id' => ['sometimes', 'exists:catalog_parts,id'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'status' => ['sometimes', 'in:open,closed'],
        ]);

        $consignment->update($validated);

        return response()->json(['data' => $consignment->fresh()]);
    }
}
```

- [ ] **Step 4: API LotController implementieren**

`app/Http/Controllers/Api/LotController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consignment;
use App\Models\Lot;
use Illuminate\Http\Request;

class LotController extends Controller
{
    public function index(Consignment $consignment)
    {
        $lots = $consignment->lots()
            ->with(['category', 'catalogType'])
            ->orderBy('sequence_number')
            ->get();

        return response()->json(['data' => $lots]);
    }

    public function byConsignorNumber(Request $request)
    {
        $request->validate([
            'consignor_number' => ['required', 'string'],
        ]);

        $lots = Lot::with(['category', 'catalogType', 'consignment'])
            ->whereHas('consignment', function ($q) use ($request) {
                $q->where('consignor_number', $request->consignor_number);
            })
            ->orderBy('sequence_number')
            ->get();

        return response()->json(['data' => $lots]);
    }
}
```

- [ ] **Step 5: API UserController implementieren**

`app/Http/Controllers/Api/UserController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
            'role' => ['required', 'in:admin,user'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'locale' => 'de',
        ]);

        return response()->json(['data' => $user], 201);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', Password::min(8)],
            'role' => ['sometimes', 'in:admin,user'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json(['data' => $user->fresh()]);
    }
}
```

- [ ] **Step 6: API-Routen registrieren**

`routes/api.php`:

```php
<?php

use App\Http\Controllers\Api\ConsignmentController;
use App\Http\Controllers\Api\LotController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\ApiKeyMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(ApiKeyMiddleware::class)->prefix('v1')->group(function () {
    // Consignments
    Route::get('/consignments', [ConsignmentController::class, 'index']);
    Route::get('/consignments/{consignment}', [ConsignmentController::class, 'show']);
    Route::post('/consignments', [ConsignmentController::class, 'store']);
    Route::put('/consignments/{consignment}', [ConsignmentController::class, 'update']);

    // Lots
    Route::get('/consignments/{consignment}/lots', [LotController::class, 'index']);
    Route::get('/lots', [LotController::class, 'byConsignorNumber']);

    // Users
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
});
```

- [ ] **Step 7: Tests ausführen**

```bash
php artisan test tests/Feature/Api/
```

Expected: Alle 10 Tests PASS.

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/Api/ routes/api.php tests/Feature/Api/
git commit -m "feat: REST API mit API-Key Auth für Dashboard-Integration (ref #TBD)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 12: TailwindCSS & Vite einrichten

**Files:**
- Modify: `tailwind.config.js`
- Modify: `resources/css/app.css`
- Modify: `vite.config.js`

- [ ] **Step 1: TailwindCSS installieren und konfigurieren**

```bash
npm install -D tailwindcss @tailwindcss/forms postcss autoprefixer
npx tailwindcss init -p
```

- [ ] **Step 2: `tailwind.config.js` anpassen**

```js
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
```

- [ ] **Step 3: `resources/css/app.css` anpassen**

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

- [ ] **Step 4: Build prüfen**

```bash
npm run build
```

Expected: Build erfolgreich, CSS und JS in `public/build/`.

- [ ] **Step 5: Commit**

```bash
git add tailwind.config.js postcss.config.js resources/css/app.css package.json package-lock.json vite.config.js public/build/
git commit -m "feat: TailwindCSS und Vite konfigurieren (ref #TBD)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

### Task 13: Alle Tests ausführen und aufräumen

**Files:**
- Alle Testdateien

- [ ] **Step 1: Komplette Test-Suite ausführen**

```bash
php artisan test
```

Expected: Alle Tests PASS. Notiere fehlschlagende Tests und behebe sie.

- [ ] **Step 2: Fehlende Sprachdateien-Einträge ergänzen**

Prüfe, ob alle `__('messages.xxx')` und `__('auth.xxx')` Referenzen in den Views auch in den Sprachdateien definiert sind. Fehlende Einträge ergänzen.

- [ ] **Step 3: `.gitignore` für `.superpowers/` ergänzen**

In `.gitignore` hinzufügen:

```
.superpowers/
```

- [ ] **Step 4: Final-Commit**

```bash
php artisan test
git add -A
git commit -m "fix: Fehlende Sprachschlüssel und Aufräumarbeiten (ref #TBD)\n\nCo-authored-by: Claude <claude@anthropic.com>"
```

---

## Zusammenfassung

| Task | Beschreibung | Tests |
|------|--------------|-------|
| 1 | Laravel-Projekt aufsetzen | — |
| 2 | Datenbank-Migrationen | — |
| 3 | Models & Factories | 9 Unit-Tests |
| 4 | Seeders | — |
| 5 | Middleware | 8 Feature-Tests |
| 6 | Authentifizierung | 7 Feature-Tests |
| 7 | Beschreiber Einlieferungen | 5 Feature-Tests |
| 8 | Beschreiber Los-Erfassung (Livewire) | 6 Feature-Tests |
| 9 | Admin Beschreiberverwaltung | 7 Feature-Tests |
| 10 | Admin Einlieferungsverwaltung | 6 Feature-Tests |
| 11 | REST API | 10 Feature-Tests |
| 12 | TailwindCSS & Vite | — |
| 13 | Finaler Test-Lauf & Aufräumen | Alle |

**Gesamt: 13 Tasks, ~58 Tests**
