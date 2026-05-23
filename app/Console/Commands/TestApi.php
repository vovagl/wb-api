<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class TestApi extends Command
{
    protected $signature = 'app:test-api';
    protected $description = 'Import orders from external API';

    public function handle()
    {
        $page = 1;
        $limit = 500;

        do {
            $response = Http::get(env('API_BASE_URL') . '/api/orders', [
                'key' => env('API_KEY'),
                'dateFrom' => now()->subDay()->format('Y-m-d'),
                'dateTo' => now()->format('Y-m-d'),
                'page' => $page,
                'limit' => $limit,
            ]);

            $data = $response->json();

            if (!is_array($data) || !isset($data['data'])) {
                $this->error('Invalid response from API');
                dump($data);
                return;
            }

            $items = $data['data'];

            if (empty($items)) {
                break;
            }

            foreach ($items as $item) {
                Order::updateOrCreate(
                    ['g_number' => $item['g_number']], // защита от дублей
                    [
                        'date' => $item['date'] ?? null,
                        'last_change_date' => $item['last_change_date'] ?? null,
                        'supplier_article' => $item['supplier_article'] ?? null,
                        'tech_size' => $item['tech_size'] ?? null,
                        'barcode' => $item['barcode'] ?? null,
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
                        'is_cancel' => $item['is_cancel'] ?? false,
                        'cancel_dt' => $item['cancel_dt'] ?? null,
                    ]
                );
            }

            $this->info("Page {$page} imported (" . count($items) . " items)");

            $page++;

            $hasNext = $data['links']['next'] ?? null;

        } while ($hasNext);

        $this->info('DONE. Import finished.');
    }
}