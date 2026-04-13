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
        return Category::where($field, 'like', "%{$this->categorySearch}%")->limit(10)->get();
    }

    public function getFilteredCatalogTypesProperty()
    {
        $locale = app()->getLocale();
        $field = "name_{$locale}";
        return CatalogType::where($field, 'like', "%{$this->catalogTypeSearch}%")->limit(10)->get();
    }

    public function render()
    {
        return view('livewire.lot-form');
    }
}
