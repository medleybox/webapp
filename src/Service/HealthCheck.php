<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\{HttpClientInterface, ResponseInterface};

class HealthCheck
{
    const TIMEOUT = 1;

    /**
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    public $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    protected function get(string $url): ResponseInterface
    {
        return $this->client->request(
            'GET',
            $url,
            ['timeout' => self::TIMEOUT]
        );
    }

    protected function head(string $url): ResponseInterface
    {
        return $this->client->request(
            'HEAD',
            $url,
            ['timeout' => self::TIMEOUT]
        );
    }

    public function getOverview(): array
    {
        try {
            return [
                'minio' => $this->minio(),
                'websocket' => $this->websocket(),
            ];
        } catch (\Exception $e) {
            return ['_error_' => $e->getMessage()];
        }
    }

    public function minio()
    {
        return false;
        // $this->get('http://mini:9000/');
    }

    public function websocket(): bool
    {
        return true;
    }
}
