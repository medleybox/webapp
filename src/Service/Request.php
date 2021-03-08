<?php

namespace App\Service;

use Symfony\Component\Form\Form;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Request
{
    const BASE_URI = 'http://vault';

    /*
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    public $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function get($url)
    {
        return $this->client->request(
            'GET',
            $url,
            ['base_uri' => self::BASE_URI]
        );
    }

    public function post($url, array $data = [])
    {
        return $this->client->request(
            'POST',
            $url,
            [
                'base_uri' => self::BASE_URI,
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
                [
                    'base_uri' => self::BASE_URI,
                    'json' => $data,
                ]
            );
        } catch (\RuntimeException $e) {
            return false;
        }

        return true;
    }
}
