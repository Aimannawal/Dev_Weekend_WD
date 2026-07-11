<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class StuntingPredictionService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.stunting_api.url', 'http://127.0.0.1:8001');
    }

    public function predict(array $data): array
    {
        $response = Http::timeout(30)
            ->post("{$this->baseUrl}/predict", $data);

        if ($response->failed()) {
            throw new Exception('FastAPI error: ' . $response->status());
        }

        return $response->json();
    }
}
