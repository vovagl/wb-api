<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('dateFrom')) {
            $query->where('date', '>=', $request->dateFrom);
        }

        if ($request->filled('dateTo')) {
            $query->where('date', '<=', $request->dateTo);
        }

        $limit = $request->get('limit', 500);

        $orders = $query->paginate($limit);

        return response()->json($orders);
    }
}