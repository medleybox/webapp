<?php

namespace App\Service;

use Symfony\Component\Form\Form;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Request
{
    const BASE_URI = 'http://vault';

    /*
     * @var string
     */
    private string $baseUrl = self::BASE_URI;

    /*
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    public $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function setBaseUrl(string $url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    public function get($url)
    {
        return $this->client->request(
            'GET',
            $url,
            ['base_uri' => $this->baseUrl]
        );
    }

    public function post($url, array $data = [])
    {
        return $this->client->request(
            'POST',
            $url,
            [
                'base_uri' => $this->baseUrl,
                'body' => $data
            ]
        );
    }

    public function delete($url, array $data = [])
    {
        try {
            $this->client->request(
                'DELETE',
                $url,
                ['base_uri' => $this->baseUrl]
            );
        } catch (\RuntimeException $e) {
            return false;
        }

        return true;
    }
}
