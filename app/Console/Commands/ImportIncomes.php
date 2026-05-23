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

    do {

        $response = Http::get(env('API_BASE_URL') . '/api/incomes', [
            'key' => env('API_KEY'),
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
                    'supplier_article' => $item['supplier_article'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['total_price'],
                ]
            );
        }

        $page++;

    } while (!empty($data));

    $this->info('Incomes imported successfully');

    return 0;
}
}