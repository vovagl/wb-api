<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiClient
{
    public function get(string $endpoint, array $params = [])
    {
        $baseUrl = rtrim(env('API_BASE_URL'), '/');

        $params['key'] = env('API_KEY');

        $response = Http::timeout(30)
            ->acceptJson()
            ->get($baseUrl . $endpoint, $params);

        return $response->json();
    }
}