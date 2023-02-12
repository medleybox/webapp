<?php

namespace App\Service;

use App\Entity\MediaFile;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Contracts\HttpClient\{HttpClientInterface, ResponseInterface};

class Request
{
    const BASE_URI = 'https://nginx/vault/';
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

    public function get(string $url): ?ResponseInterface
    {
        try {
            $get = $this->client->request(
                'GET',
                $url,
                ['base_uri' => $this->baseUrl, 'timeout' => self::TIMEOUT]
            );

            return $get;
        } catch (ServerException $e) {
            //
        }

        return null;
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
    public function post(string $url, array $data = []): ?ResponseInterface
    {
        try {
            $post = $this->client->request(
                'POST',
                $url,
                [
                    'base_uri' => $this->baseUrl,
                    'timeout' => self::TIMEOUT,
                    'body' => $data
                ]
            );

            return $post;
        } catch (\RuntimeException $e) {
            //
        }

        return null;
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

    public function updateVaultDownload(MediaFile $entry, $filename)
    {
        return $this->post(
            "entry/update-download/{$entry->getUuid()}",
            [
                'filename' => $filename
            ]
        );
    }

    public function refreshMediaList(): ResponseInterface
    {
        return $this->get('websocket/refreshMediaList');
    }

    public function refreshLatestList(): ResponseInterface
    {
        return $this->get('websocket/refreshLatestList');
    }
}
