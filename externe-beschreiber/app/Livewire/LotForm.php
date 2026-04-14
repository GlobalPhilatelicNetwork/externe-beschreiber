<?php

namespace App\Livewire;

use App\Models\CatalogType;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Consignment;
use App\Models\Destination;
use App\Models\GroupingCategory;
use App\Models\PackType;
use Livewire\Component;

class LotForm extends Component
{
    public Consignment $consignment;

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

    // --- Category methods ---

    public function updatedCategorySearch(): void
    {
        $this->showCategoryDropdown = strlen($this->categorySearch) > 0;
    }

    public function selectCategory(int $id): void
    {
        if (!in_array($id, $this->selectedCategoryIds)) {
            $this->selectedCategoryIds[] = $id;
        }
        $this->categorySearch = '';
        $this->showCategoryDropdown = false;
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
        $this->selectedGroupingCategoryId = null;
    }

    public function selectGroupingCategory(int $id, string $name): void
    {
        $this->selectedGroupingCategoryId = $id;
        $this->groupingCategorySearch = $name;
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
        return Destination::where($field, 'like', "%{$this->destinationSearch}%")
            ->whereNotIn('id', $this->selectedDestinationIds)
            ->limit(10)
            ->get();
    }

    public function getSelectedDestinationsProperty()
    {
        if (empty($this->selectedDestinationIds)) {
            return collect();
        }
        return Destination::whereIn('id', $this->selectedDestinationIds)->get();
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
