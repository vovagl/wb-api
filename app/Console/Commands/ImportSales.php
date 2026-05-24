<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Sale;

class ImportSales extends Command
{
    protected $signature = 'app:import-sales';
    protected $description = 'Import sales from API';

    public function handle()
    {
        $page = 1;
        $lastPage = 1;
        $baseUrl = config('services.api.base_url');
        $apiKey = config('services.api.key');

        do {
            $response = Http::timeout(60)
                ->get($baseUrl . '/api/sales', [
                'key' => $apiKey,
                'dateFrom' => now()->subDay()->format('Y-m-d'),
                'dateTo' => now()->format('Y-m-d'),
                'page' => $page,
                'limit' => 500,
            ]);

            if (!$response->ok()) {
                $this->error("HTTP error on page {$page}");
                return 1;
            }

            $data = $response->json();

            if (!isset($data['data']) || !is_array($data['data'])) {
                $this->error('Invalid API response');
                dd($data);
                return 1;
            }

            foreach ($data['data'] as $item) {
                if (!isset($item['g_number'])) {
                    continue;
                }

                Sale::updateOrCreate(
                    [
                        'g_number' => $item['g_number'],
                    ],
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

            $lastPage = $data['last_page'] ?? 1;
            $page++;

        } while ($page <= $lastPage);

        $this->info('Sales imported successfully');
        return 0;
    }
}