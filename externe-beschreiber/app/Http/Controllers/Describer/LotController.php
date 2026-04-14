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
            'conditions' => Condition::orderBy('sort_order')->get(),
            'destinations' => Destination::all(),
            'groupingCategories' => GroupingCategory::forSale($consignment->sale_id)->get(),
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
