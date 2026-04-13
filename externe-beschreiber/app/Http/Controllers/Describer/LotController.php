<?php
namespace App\Http\Controllers\Describer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLotRequest;
use App\Http\Requests\UpdateLotRequest;
use App\Models\Consignment;
use App\Models\Lot;
use App\Models\Category;
use App\Models\CatalogType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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
        Gate::authorize('manageLots', $consignment);
        $lot->load(['category', 'catalogType']);
        $categories = Category::all();
        $catalogTypes = CatalogType::all();
        return view('describer.lots.edit', compact('consignment', 'lot', 'categories', 'catalogTypes'));
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
        Gate::authorize('manageLots', $consignment);
        $lot->delete();
        return redirect()
            ->route('describer.consignments.show', $consignment)
            ->with('success', __('messages.lot_deleted'));
    }
}
