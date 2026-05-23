<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Stock;

class ImportStocks extends Command
{
    protected $signature = 'app:import-stocks';
    protected $description = 'Import stocks from API';

    public function handle()
{
    $page = 1;
    $date = now()->toDateString();

    $baseUrl = config('services.api.base_url');
    $apiKey = config('services.api.key');

    do {
        $response = Http::timeout(30)->get($baseUrl . '/api/stocks',
            [
                'key' => $apiKey,
                'dateFrom' => $date,
                'dateTo'   => $date,
                'page'     => $page,
                'limit'    => 500,
            ]
        );

        if (!$response->ok()) {
            $this->error("API error on page {$page}");
            $this->error($response->status());
            $this->error($response->body());
            return 1;
        }

        $data = $response->json();

        foreach ($data['data'] ?? [] as $item) {

            if (!isset($item['barcode'], $item['nm_id'])) {
                continue;
            }

            Stock::updateOrCreate(
                [
                    'barcode' => $item['barcode'],
                    'warehouse_name' => $item['warehouse_name'] ?? null,
                    'nm_id' => $item['nm_id'],
                ],
                [
                    'quantity' => $item['quantity'] ?? 0,
                ]
            );
        }

        $lastPage = $data['last_page'] ?? 1;
        $page++;

    } while ($page <= $lastPage);

    $this->info('Stocks imported successfully');
}
}