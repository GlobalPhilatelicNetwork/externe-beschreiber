<?php
namespace App\Http\Controllers\Describer;

use App\Http\Controllers\Controller;
use App\Models\Consignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ConsignmentController extends Controller
{
    public function index(Request $request)
    {
        $consignments = $request->user()->isAdmin()
            ? Consignment::with(['user', 'catalogPart'])->latest()->get()
            : $request->user()->consignments()->with('catalogPart')->latest()->get();

        return view('describer.consignments.index', compact('consignments'));
    }

    public function show(Request $request, Consignment $consignment)
    {
        Gate::authorize('view', $consignment);
        $consignment->load(['lots.category', 'lots.catalogType', 'catalogPart']);
        return view('describer.consignments.show', compact('consignment'));
    }
}
