<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\{HttpClientInterface, ResponseInterface};

class Request
{
    const BASE_URI = 'http://vault';
    const TIMEOUT = 30;

    /**
     * @var string
     */
    private string $baseUrl = self::BASE_URI;

    /**
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    public $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function setBaseUrl(string $url): self
    {
        $this->baseUrl = $url;

        return $this;
    }

    public function get(string $url): ResponseInterface
    {
        return $this->client->request(
            'GET',
            $url,
            ['base_uri' => $this->baseUrl, 'timeout' => self::TIMEOUT]
        );
    }

    public function head(string $url): ResponseInterface
    {
        return $this->client->request(
            'HEAD',
            $url,
            ['base_uri' => $this->baseUrl, 'timeout' => self::TIMEOUT]
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function post(string $url, array $data = []): ResponseInterface
    {
        return $this->client->request(
            'POST',
            $url,
            [
                'base_uri' => $this->baseUrl,
                'timeout' => self::TIMEOUT,
                'body' => $data
            ]
        );
    }

    public function delete(string $url): bool
    {
        try {
            $this->client->request(
                'DELETE',
                $url,
                ['base_uri' => $this->baseUrl, 'timeout' => self::TIMEOUT]
            );
        } catch (\RuntimeException $e) {
            return false;
        }

        return true;
    }

    public function refreshMediaList(): ResponseInterface
    {
        return $this->get('websocket/refreshMediaList');
    }
}
