<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;
use Illuminate\Support\Facades\Http;

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
    public function sync()
{
    $apiKey = config('services.api.key');
    $baseUrl = config('services.api.base_url');

    $response = Http::get($baseUrl . '/api/sales', [
        'key' => $apiKey,
        'dateFrom' => now()->subDays(7)->format('Y-m-d'),
        'dateTo' => now()->format('Y-m-d'),
        'page' => 1,
        'limit' => 20,
    ]);

    $data = $response->json()['data'] ?? [];

    foreach ($data as $item) {
        Income::updateOrCreate(
            ['income_id' => $item['income_id']],
            [
                'date' => $item['date'],
                'last_change_date' => $item['last_change_date'],
                'supplier_article' => $item['supplier_article'],
                'tech_size' => $item['tech_size'],
                'barcode' => $item['barcode'],
                'quantity' => $item['quantity'] ?? 0,
                'total_price' => $item['total_price'] ?? 0,
                'warehouse_name' => $item['warehouse_name'],
                'nm_id' => $item['nm_id'],
            ]
        );
    }

    return response()->json([
        'message' => 'ok',
        'count' => count($data)
    ]);
}
}
    