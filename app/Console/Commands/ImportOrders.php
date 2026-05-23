<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Order;

class ImportOrders extends Command
{
    protected $signature = 'app:import-orders';
    protected $description = 'Import orders from API';

    public function handle()
    {
        $page = 1;

        do {
            $response = Http::timeout(60)->get(env('API_BASE_URL') . '/api/orders', [
                'key' => env('API_KEY'),
                'dateFrom' => now()->subDays(3)->format('Y-m-d'),
                'dateTo' => now()->format('Y-m-d'),
                'page' => $page,
                'limit' => 500,
            ]);

            if (!$response->successful()) {
                $this->error("HTTP error: " . $response->status());
                dump($response->body());
                return 1;
            }

            $json = $response->json();

            if (!is_array($json)) {
                $this->error('Invalid JSON response');
                dump($response->body());
                return 1;
            }

            $data = $json['data'] ?? [];

            if (!is_array($data)) {
                $this->error('Missing data key');
                dump($json);
                return 1;
            }

            if (count($data) === 0) {
                break;
            }

            foreach ($data as $item) {

                if (!isset($item['g_number'])) {
                    continue;
                }

                Order::updateOrCreate(
                    [
                        'g_number' => $item['g_number'],
                        'barcode' => $item['barcode'] ?? null,
                    ],
                    [
                        'date' => $item['date'] ?? null,
                        'last_change_date' => $item['last_change_date'] ?? null,
                        'supplier_article' => $item['supplier_article'] ?? null,
                        'tech_size' => $item['tech_size'] ?? null,
                        'total_price' => $item['total_price'] ?? 0,
                        'discount_percent' => $item['discount_percent'] ?? 0,
                        'warehouse_name' => $item['warehouse_name'] ?? null,
                        'oblast' => $item['oblast'] ?? null,
                        'income_id' => $item['income_id'] ?? null,
                        'odid' => $item['odid'] ?? null,
                        'nm_id' => $item['nm_id'] ?? null,
                        'subject' => $item['subject'] ?? null,
                        'category' => $item['category'] ?? null,
                        'brand' => $item['brand'] ?? null,
                        'is_cancel' => $item['is_cancel'] ?? 0,
                        'cancel_dt' => $item['cancel_dt'] ?? null,
                    ]
                );
            }

            $page++;

        } while (true);

        $this->info('Orders imported successfully');

        return 0;
    }
}