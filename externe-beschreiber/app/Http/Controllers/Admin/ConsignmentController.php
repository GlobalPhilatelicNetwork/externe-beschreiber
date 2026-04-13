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
        if ($request->filled('status')) { $query->where('status', $request->status); }
        if ($request->filled('user_id')) { $query->where('user_id', $request->user_id); }

        $consignments = $query->latest()->get();
        $describers = User::where('role', 'user')->orderBy('name')->get();
        $catalogParts = CatalogPart::all();
        $defaultCatalogPart = CatalogPart::where('is_default', true)->first();

        return view('admin.consignments.index', compact('consignments', 'describers', 'catalogParts', 'defaultCatalogPart'));
    }

    public function store(StoreConsignmentRequest $request)
    {
        Consignment::create([
            'consignor_number' => $request->consignor_number,
            'internal_nid' => $request->internal_nid,
            'sale_id' => $request->sale_id,
            'start_number' => $request->start_number,
            'next_number' => $request->start_number,
            'catalog_part_id' => $request->catalog_part_id,
            'user_id' => $request->user_id,
            'status' => 'open',
        ]);
        return redirect()->route('admin.consignments.index')->with('success', __('messages.consignment_created'));
    }

    public function close(Consignment $consignment)
    {
        $consignment->update(['status' => 'closed']);
        return redirect()->route('admin.consignments.index')->with('success', __('messages.consignment_closed_success'));
    }
}
