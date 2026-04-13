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
        if ($request->filled('status')) { $query->where('status', $request->status); }
        if ($request->filled('consignor_number')) { $query->where('consignor_number', $request->consignor_number); }
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
