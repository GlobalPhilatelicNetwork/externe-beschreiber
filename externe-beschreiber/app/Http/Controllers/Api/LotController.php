<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consignment;
use App\Models\Lot;
use Illuminate\Http\Request;

class LotController extends Controller
{
    public function index(Consignment $consignment)
    {
        $lots = $consignment->lots()->with(['category', 'catalogType'])->orderBy('sequence_number')->get();
        return response()->json(['data' => $lots]);
    }

    public function byConsignorNumber(Request $request)
    {
        $request->validate(['consignor_number' => ['required', 'string']]);
        $lots = Lot::with(['category', 'catalogType', 'consignment'])
            ->whereHas('consignment', fn($q) => $q->where('consignor_number', $request->consignor_number))
            ->orderBy('sequence_number')->get();
        return response()->json(['data' => $lots]);
    }
}
