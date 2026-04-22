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
        $lot = null;

        DB::transaction(function () use ($request, $consignment, &$lot) {
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
                    $lot->catalogEntries()->create(collect($entry)->only(['catalog_type_id', 'catalog_number'])->toArray());
                }
            }

            if ($request->packages) {
                foreach ($request->packages as $package) {
                    $lot->packages()->create(collect($package)->only(['pack_type_id', 'pack_number', 'pack_note'])->toArray());
                }
            }

            $consignment->increment('next_number');
        });

        if ($request->input('_action') === 'copy' && $lot) {
            $copyFields = $request->input('_copy_fields', []);
            $copyData = $this->buildCopyData($lot, $copyFields);
            session()->flash('lot_copy_data', $copyData);
            session()->flash('open_lot_form', true);
        }

        return redirect()
            ->route('describer.consignments.show', $consignment)
            ->with('success', __('messages.lot_created'));
    }

    private function buildCopyData(Lot $lot, array $fields): array
    {
        $lot->load(['categories', 'conditions', 'destinations', 'catalogEntries', 'packages', 'groupingCategory']);
        $data = [];

        foreach ($fields as $field) {
            switch ($field) {
                case 'categories':
                    $data['category_ids'] = $lot->categories->pluck('id')->toArray();
                    break;
                case 'lot_type':
                    $data['lot_type'] = $lot->lot_type;
                    break;
                case 'grouping_category':
                    $data['grouping_category_id'] = $lot->grouping_category_id;
                    break;
                case 'conditions':
                    $data['condition_ids'] = $lot->conditions->pluck('id')->toArray();
                    break;
                case 'destinations':
                    $data['destination_ids'] = $lot->destinations->pluck('id')->toArray();
                    break;
                case 'catalog_entries':
                    $data['catalog_entries'] = $lot->catalogEntries->map(fn($e) => [
                        'catalog_type_id' => (string) $e->catalog_type_id,
                        'catalog_number' => $e->catalog_number,
                    ])->toArray();
                    break;
                case 'packaging':
                    $data['packages'] = $lot->packages->map(fn($p) => [
                        'pack_type_id' => (string) $p->pack_type_id,
                        'pack_number' => $p->pack_number,
                        'pack_note' => $p->pack_note ?? '',
                    ])->toArray();
                    break;
                case 'starting_price':
                    $data['starting_price'] = number_format($lot->starting_price, 2, '.', '');
                    break;
                case 'description':
                    $data['description'] = $lot->description ?? '';
                    break;
                case 'provenance':
                    $data['provenance'] = $lot->provenance ?? '';
                    break;
                case 'epos':
                    $data['epos'] = $lot->epos ?? '';
                    break;
                case 'notes':
                    $data['notes'] = $lot->notes ?? '';
                    break;
                case 'bid_lot':
                    $data['is_bid_lot'] = (bool) $lot->is_bid_lot;
                    break;
            }
        }

        return $data;
    }

    public function edit(Consignment $consignment, Lot $lot)
    {
        Gate::authorize('manageLots', $consignment);
        abort_if($lot->consignment_id !== $consignment->id, 403);

        return view('describer.lots.edit', [
            'consignment' => $consignment,
            'lot' => $lot,
        ]);
    }

    public function update(UpdateLotRequest $request, Consignment $consignment, Lot $lot)
    {
        abort_if($lot->consignment_id !== $consignment->id, 403);

        DB::transaction(function () use ($request, $lot) {
            $lot->update($request->safe()->only(['lot_type', 'grouping_category_id', 'description', 'provenance', 'epos', 'starting_price', 'is_bid_lot', 'notes']));

            $lot->categories()->sync($request->category_ids);
            $lot->conditions()->sync($request->condition_ids);
            $lot->destinations()->sync($request->destination_ids ?? []);

            $lot->catalogEntries()->delete();
            if ($request->catalog_entries) {
                foreach ($request->catalog_entries as $entry) {
                    $lot->catalogEntries()->create(collect($entry)->only(['catalog_type_id', 'catalog_number'])->toArray());
                }
            }

            $lot->packages()->delete();
            if ($request->packages) {
                foreach ($request->packages as $package) {
                    $lot->packages()->create(collect($package)->only(['pack_type_id', 'pack_number', 'pack_note'])->toArray());
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
        abort_if($lot->consignment_id !== $consignment->id, 403);
        $lot->delete();

        return redirect()
            ->route('describer.consignments.show', $consignment)
            ->with('success', __('messages.lot_deleted'));
    }
}
