<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::query();

        if ($request->filled('warehouse_name')) {
        $query->where('warehouse_name', $request->warehouse_name);
    }

    if ($request->filled('nm_id')) {
        $query->where('nm_id', $request->nm_id);
    }

    return $query->paginate(500);
    }
}