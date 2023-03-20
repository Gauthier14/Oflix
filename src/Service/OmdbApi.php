<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OmdbApi
{

    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function fetchPoster(string $title)
    {
        $response = $this->httpClient->request(
            'GET',
            'https://www.omdbapi.com/',
            [
                'query' => [
                    't' => $title,
                    'apiKey' => '9015613b',
                ]
            ]
                );
                $responseArray = $response->toArray();
                if (!array_key_exists('Poster', $responseArray)) {
                    return 'https://upload.wikimedia.org/wikipedia/commons/6/64/Poster_not_available.jpg';
                }
                return $responseArray['Poster'];
    }
}