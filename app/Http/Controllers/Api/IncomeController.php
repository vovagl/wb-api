<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;

class IncomeController extends Controller
{
     public function index(Request $request)
    {
        $query = Income::query();

        if ($request->filled('dateFrom')) {
            $query->whereDate('date', '>=', $request->dateFrom);
        }

        if ($request->filled('dateTo')) {
            $query->whereDate('date', '<=', $request->dateTo);
        }

        return response()->json(
            $query->paginate($request->limit ?? 500)
        );
    }
    //
}
