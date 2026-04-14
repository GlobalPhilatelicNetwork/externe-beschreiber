<?php

namespace App\Livewire;

use App\Models\CatalogType;
use App\Models\Category;
use App\Models\CategoryCatalogTypeMapping;
use App\Models\Condition;
use App\Models\Consignment;
use App\Models\GroupingCategory;
use App\Models\PackType;
use App\Models\Lot;
use Livewire\Component;

class LotForm extends Component
{
    public Consignment $consignment;
    public ?Lot $lot = null;
    public bool $editMode = false;

    // Lot type
    public string $lot_type = 'single';

    // Category multi-select
    public string $categorySearch = '';
    public array $selectedCategoryIds = [];
    public bool $showCategoryDropdown = false;

    // Grouping category single-select
    public string $groupingCategorySearch = '';
    public ?int $selectedGroupingCategoryId = null;
    public bool $showGroupingCategoryDropdown = false;

    // Destination multi-select
    public string $destinationSearch = '';
    public array $selectedDestinationIds = [];
    public bool $showDestinationDropdown = false;

    // Conditions (toggle buttons)
    public array $selectedConditionIds = [];

    // Rich text fields (synced via JS on submit)
    public string $description = '';
    public string $provenance = '';

    // Simple fields
    public string $epos = '';
    public string $starting_price = '';
    public bool $is_bid_lot = false;
    public string $notes = '';

    // Dynamic rows
    public array $catalogEntries = [['catalog_type_id' => '', 'catalog_number' => '']];
    public array $packageEntries = [];

    public function mount(Consignment $consignment, ?Lot $lot = null): void
    {
        $this->consignment = $consignment;

        if ($lot && $lot->exists) {
            $this->lot = $lot;
            $this->editMode = true;
            $lot->load(['categories', 'conditions', 'destinations', 'catalogEntries', 'packages', 'groupingCategory']);

            $this->lot_type = $lot->lot_type;
            $this->selectedCategoryIds = $lot->categories->pluck('id')->toArray();
            $this->selectedConditionIds = $lot->conditions->pluck('id')->toArray();
            $this->selectedDestinationIds = $lot->destinations->pluck('id')->toArray();
            $this->description = $lot->description ?? '';
            $this->provenance = $lot->provenance ?? '';
            $this->epos = $lot->epos ?? '';
            $this->starting_price = (string) $lot->starting_price;
            $this->is_bid_lot = (bool) $lot->is_bid_lot;
            $this->notes = $lot->notes ?? '';

            if ($lot->groupingCategory) {
                $this->selectedGroupingCategoryId = $lot->grouping_category_id;
                $this->groupingCategorySearch = $lot->groupingCategory->name;
            }

            $this->catalogEntries = $lot->catalogEntries->map(fn($e) => [
                'catalog_type_id' => (string) $e->catalog_type_id,
                'catalog_number' => $e->catalog_number,
            ])->toArray();
            if (empty($this->catalogEntries)) {
                $this->catalogEntries = [['catalog_type_id' => '', 'catalog_number' => '']];
            }

            $this->packageEntries = $lot->packages->map(fn($p) => [
                'pack_type_id' => (string) $p->pack_type_id,
                'number' => $p->pack_number,
                'notes' => $p->pack_note ?? '',
            ])->toArray();
        } elseif (old('lot_type')) {
            // Restore form data after validation error
            $this->lot_type = old('lot_type', 'single');
            $this->selectedCategoryIds = array_map('intval', old('category_ids') ?? []);
            $this->selectedConditionIds = array_map('intval', old('condition_ids') ?? []);
            $this->selectedDestinationIds = array_map('intval', old('destination_ids') ?? []);
            $this->description = old('description', '');
            $this->provenance = old('provenance', '');
            $this->epos = old('epos', '');
            $this->starting_price = old('starting_price', '');
            $this->is_bid_lot = (bool) old('is_bid_lot', false);
            $this->notes = old('notes', '');

            $gcId = old('grouping_category_id');
            if ($gcId) {
                $this->selectedGroupingCategoryId = (int) $gcId;
                $gc = GroupingCategory::find($gcId);
                $this->groupingCategorySearch = $gc?->name ?? '';
            }

            $oldCatalog = old('catalog_entries') ?? [];
            if (!empty($oldCatalog)) {
                $this->catalogEntries = array_values(array_map(fn($e) => [
                    'catalog_type_id' => $e['catalog_type_id'] ?? '',
                    'catalog_number' => $e['catalog_number'] ?? '',
                ], $oldCatalog));
            }

            $oldPackages = old('packages') ?? [];
            if (!empty($oldPackages)) {
                $this->packageEntries = array_values(array_map(fn($p) => [
                    'pack_type_id' => $p['pack_type_id'] ?? '',
                    'number' => $p['pack_number'] ?? '',
                    'notes' => $p['pack_note'] ?? '',
                ], $oldPackages));
            }
        }
    }

    // --- Category methods ---

    public function updatedCategorySearch(): void
    {
        $this->showCategoryDropdown = strlen($this->categorySearch) > 0;
    }

    public function selectCategory(int $id): void
    {
        $isFirst = empty($this->selectedCategoryIds);
        if (!in_array($id, $this->selectedCategoryIds)) {
            $this->selectedCategoryIds[] = $id;
        }
        $this->categorySearch = '';
        $this->showCategoryDropdown = false;

        if ($isFirst) {
            $this->autoCatalogTypeFromCategory($id);
        }
    }

    private function autoCatalogTypeFromCategory(int $categoryId): void
    {
        $category = Category::find($categoryId);
        if (!$category) return;

        $locale = app()->getLocale();
        $name = $locale === 'de' ? $category->name_de : $category->name_en;
        $catalogTypeId = CategoryCatalogTypeMapping::findCatalogTypeForCategory($name);

        if ($catalogTypeId && isset($this->catalogEntries[0]) && empty($this->catalogEntries[0]['catalog_type_id'])) {
            $this->catalogEntries[0]['catalog_type_id'] = (string) $catalogTypeId;
        }
    }

    public function removeCategory(int $id): void
    {
        $this->selectedCategoryIds = array_values(
            array_filter($this->selectedCategoryIds, fn($cid) => $cid !== $id)
        );
    }

    public function getFilteredCategoriesProperty()
    {
        if (strlen($this->categorySearch) === 0) {
            return collect();
        }
        $locale = app()->getLocale();
        $field = "name_{$locale}";
        return Category::where($field, 'like', "%{$this->categorySearch}%")
            ->whereNotIn('id', $this->selectedCategoryIds)
            ->limit(10)
            ->get();
    }

    public function getSelectedCategoriesProperty()
    {
        if (empty($this->selectedCategoryIds)) {
            return collect();
        }
        return Category::whereIn('id', $this->selectedCategoryIds)->get();
    }

    // --- Grouping Category methods ---

    public function updatedGroupingCategorySearch(): void
    {
        $this->showGroupingCategoryDropdown = strlen($this->groupingCategorySearch) > 0;
        if (strlen($this->groupingCategorySearch) === 0) {
            $this->selectedGroupingCategoryId = null;
        }
    }

    public function selectGroupingCategory(int $id): void
    {
        $gc = GroupingCategory::find($id);
        $this->selectedGroupingCategoryId = $id;
        $this->groupingCategorySearch = $gc?->name ?? '';
        $this->showGroupingCategoryDropdown = false;
    }

    public function clearGroupingCategory(): void
    {
        $this->selectedGroupingCategoryId = null;
        $this->groupingCategorySearch = '';
    }

    public function getFilteredGroupingCategoriesProperty()
    {
        if (strlen($this->groupingCategorySearch) === 0) {
            return collect();
        }
        $locale = app()->getLocale();
        $field = "name_{$locale}";
        $query = GroupingCategory::forSale($this->consignment->sale_id)
            ->where($field, 'like', "%{$this->groupingCategorySearch}%");
        return $query->limit(10)->get();
    }

    // --- Destination methods ---

    public function updatedDestinationSearch(): void
    {
        $this->showDestinationDropdown = strlen($this->destinationSearch) > 0;
    }

    public function selectDestination(int $id): void
    {
        if (!in_array($id, $this->selectedDestinationIds)) {
            $this->selectedDestinationIds[] = $id;
        }
        $this->destinationSearch = '';
        $this->showDestinationDropdown = false;
    }

    public function removeDestination(int $id): void
    {
        $this->selectedDestinationIds = array_values(
            array_filter($this->selectedDestinationIds, fn($did) => $did !== $id)
        );
    }

    public function getFilteredDestinationsProperty()
    {
        if (strlen($this->destinationSearch) === 0) {
            return collect();
        }
        $locale = app()->getLocale();
        $field = "name_{$locale}";
        return Category::where($field, 'like', "%{$this->destinationSearch}%")
            ->whereNotIn('id', $this->selectedDestinationIds)
            ->limit(10)
            ->get();
    }

    public function getSelectedDestinationsProperty()
    {
        if (empty($this->selectedDestinationIds)) {
            return collect();
        }
        return Category::whereIn('id', $this->selectedDestinationIds)->get();
    }

    // --- Condition toggle ---

    public function toggleCondition(int $id): void
    {
        if (in_array($id, $this->selectedConditionIds)) {
            $this->selectedConditionIds = array_values(
                array_filter($this->selectedConditionIds, fn($cid) => $cid !== $id)
            );
        } else {
            $this->selectedConditionIds[] = $id;
        }
    }

    // --- Bid lot toggle ---

    public function updatedIsBidLot(): void
    {
        if ($this->is_bid_lot) {
            $this->starting_price = '0';
        }
    }

    // --- Dynamic catalog entries ---

    public function addCatalogEntry(): void
    {
        $this->catalogEntries[] = ['catalog_type_id' => '', 'catalog_number' => ''];
    }

    public function removeCatalogEntry(int $index): void
    {
        unset($this->catalogEntries[$index]);
        $this->catalogEntries = array_values($this->catalogEntries);
        if (empty($this->catalogEntries)) {
            $this->catalogEntries = [['catalog_type_id' => '', 'catalog_number' => '']];
        }
    }

    // --- Dynamic package entries ---

    public function addPackageEntry(): void
    {
        $this->packageEntries[] = ['pack_type_id' => '', 'number' => '', 'notes' => ''];
    }

    public function removePackageEntry(int $index): void
    {
        unset($this->packageEntries[$index]);
        $this->packageEntries = array_values($this->packageEntries);
    }

    // --- Render ---

    public function render()
    {
        return view('livewire.lot-form', [
            'conditions' => Condition::orderBy('sort_order')->get(),
            'catalogTypes' => CatalogType::all(),
            'packTypes' => PackType::all(),
        ]);
    }
}
