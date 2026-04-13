<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consignment;
use App\Models\Lot;
use Illuminate\Http\Request;

class LotController extends Controller
{
    private array $lotRelations = [
        'categories',
        'conditions',
        'destinations',
        'catalogEntries.catalogType',
        'packages.packType',
        'groupingCategory',
    ];

    public function index(Consignment $consignment)
    {
        $lots = $consignment->lots()
            ->with($this->lotRelations)
            ->orderBy('sequence_number')
            ->get();
        return response()->json(['data' => $lots]);
    }

    public function byConsignorNumber(Request $request)
    {
        $request->validate(['consignor_number' => ['required', 'string']]);
        $lots = Lot::with(array_merge($this->lotRelations, ['consignment']))
            ->whereHas('consignment', fn($q) => $q->where('consignor_number', $request->consignor_number))
            ->orderBy('sequence_number')
            ->get();
        return response()->json(['data' => $lots]);
    }
}
