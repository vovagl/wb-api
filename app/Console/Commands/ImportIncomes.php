<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Income;

class ImportIncomes extends Command
{
    protected $signature = 'app:import-incomes';
    protected $description = 'Import incomes from external API';

    public function handle()
{
    $page = 1;
    $baseUrl = config('services.api.base_url');
    $apiKey = config('services.api.key');

    do {

        $response = Http::timeout(60)
            ->get($baseUrl . '/api/incomes', [
            'key' => $apiKey,
            'dateFrom' => now()->subMonths(3)->format('Y-m-d'),
            'dateTo' => now()->format('Y-m-d'),
            'page' => $page,
            'limit' => 500,
        ]);

        $json = $response->json();

        if (!isset($json['data']) || !is_array($json['data'])) {
            $this->error('API error or invalid response');
            dump($json);
            return 1;
        }

        $data = $json['data'];

        foreach ($data as $item) {

            if (!isset($item['income_id'])) {
                continue;
            }

            Income::updateOrCreate(
                [
                    'income_id' => $item['income_id'],
                ],
                [
                'date' => $item['date'],
                'last_change_date' => $item['last_change_date'] ?? null,
                'supplier_article' => $item['supplier_article'] ?? null,
                'tech_size' => $item['tech_size'] ?? null,
                'barcode' => $item['barcode'] ?? null,
                'quantity' => $item['quantity'] ?? 0,
                'total_price' => $item['total_price'] ?? 0,
                'date_close' => $item['date_close'] ?? null,
                'warehouse_name' => $item['warehouse_name'] ?? null,
                'nm_id' => $item['nm_id'] ?? null,
                ]
            );
        }

        $page++;

    } while (!empty($data));

    $this->info('Incomes imported successfully');

    return 0;
}
}