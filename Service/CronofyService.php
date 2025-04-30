<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CronofyService
{
    private $client;
    private $clientId;
    private $clientSecret;
    private $accessToken;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->clientId = $_ENV['CRONOFY_CLIENT_ID']; // Accès via la variable d'environnement
        $this->clientSecret = $_ENV['CRONOFY_CLIENT_SECRET']; // Accès via la variable d'environnement
        $this->accessToken = $_ENV['CRONOFY_TON_ACCESS_TOKEN=']; // À remplacer après authentification OAuth2
    }

    public function createEvent(string $calendarId, string $summary, \DateTimeInterface $start, \DateTimeInterface $end)
    {
        $response = $this->client->request('POST', 'https://api.cronofy.com/v1/calendars/' . $calendarId . '/events', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'event_id' => uniqid(),
                'summary' => $summary,
                'start' => $start->format('c'),
                'end' => $end->format('c'),
                'tzid' => 'Europe/Paris',
            ]
        ]);

        return $response->getStatusCode();
    }
}



?>