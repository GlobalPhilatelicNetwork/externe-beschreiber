<?php
namespace App\Http\Controllers\Describer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLotRequest;
use App\Http\Requests\UpdateLotRequest;
use App\Models\Consignment;
use App\Models\Lot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class LotController extends Controller
{
    public function store(StoreLotRequest $request, Consignment $consignment)
    {
        DB::transaction(function () use ($request, $consignment) {
            $lot = $consignment->lots()->create(array_merge(
                $request->safe()->only(['lot_type', 'grouping_category_id', 'description', 'provenance', 'epos', 'starting_price', 'is_bid_lot', 'notes']),
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

        return view('describer.lots.edit', [
            'consignment' => $consignment,
            'lot' => $lot,
        ]);
    }

    public function update(UpdateLotRequest $request, Consignment $consignment, Lot $lot)
    {
        DB::transaction(function () use ($request, $lot) {
            $lot->update($request->safe()->only(['lot_type', 'grouping_category_id', 'description', 'provenance', 'epos', 'starting_price', 'is_bid_lot', 'notes']));

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
