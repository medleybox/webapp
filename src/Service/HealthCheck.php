<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\{HttpClientInterface, ResponseInterface};

class HealthCheck
{
    const TIMEOUT = 1;

    public function __construct(
        private HttpClientInterface $client,
        private string $mUrl,
        private string $mKey
    ) {
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
                'meilisearch' => $this->meilisearchStatus()
            ];
        } catch (\Exception $e) {
            return ['_error_' => $e->getMessage()];
        }
    }

    public function minio(): bool
    {
        return true;
    }

    public function meilisearchStatus(): array
    {
        $health = $this->meilisearch('/health');
        $health['available'] = false;
        if (array_key_exists('status', $health) && $health['status'] === 'available') {
            $health['available'] = true;
        }

        return $health;
    }

    public function meilisearchVersion(): array
    {
        $version = [
            'meilisearch' => '',
            'meilisearch_extra' => null,
        ];

        $request = $this->meilisearch('/version');
        $version['meilisearch_extra'] = $request;
        if (array_key_exists('pkgVersion', $request)) {
            $version['meilisearch'] = $request['pkgVersion'];
        }

        return $version;
    }

    private function meilisearch(string $path): array
    {
        try {
            return $this->client->request(
                'GET',
                "{$this->mUrl}{$path}",
                [
                    'auth_bearer' => $this->mKey,
                    'timeout' => self::TIMEOUT
                ]
            )->toArray();
        } catch (\Exception $e) {
            return ['_error_' => $e->getMessage()];
        }
    }

    public function websocket(): bool
    {
        return true;
    }
}
