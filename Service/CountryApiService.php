<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CountryApiService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient, CacheInterface $cache)
    {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    public function getAllCountries(): array
    {
        return $this->cache->get('countries_list', function (ItemInterface $item) {
            $item->expiresAfter(86400); // 1 jour
            $response = $this->httpClient->request('GET', 'https://restcountries.com/v3.1/all');
            $data = $response->toArray();
            $countryNames = [];

            foreach ($data as $country) {
                if (isset($country['name']['common'])) {
                    $countryNames[] = $country['name']['common'];
                }
            }

            sort($countryNames);
            return $countryNames;
        });
    }
}





?>