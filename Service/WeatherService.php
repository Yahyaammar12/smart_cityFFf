<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private HttpClientInterface $client;
    private string $apiKey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiKey = '1570ecfb4d4294094f6b85ed299342bd'; // Remplace par ta vraie clé API
    }

    public function getTemperature(string $city = 'Tunis'): ?float
    {
        $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&units=metric&appid={$this->apiKey}";

        $response = $this->client->request('GET', $url);
        $data = $response->toArray();

        return $data['main']['temp'] ?? null;
    }
}



?>