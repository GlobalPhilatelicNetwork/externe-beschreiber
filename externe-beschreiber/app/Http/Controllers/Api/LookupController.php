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
