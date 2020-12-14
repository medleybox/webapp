<?php

namespace App\Service;

use Symfony\Component\Form\Form;
use Xigen\Bundle\GuzzleBundle\Service\GuzzleClient;

class Request
{
    const BASE_URI = 'http://vault';

    /*
     * @var \Xigen\Bundle\GuzzleBundle\Service\GuzzleClient
     */
    public $guzzle;

    public function __construct(GuzzleClient $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function get($url)
    {
        return $this->guzzle->request(
            'GET',
            $url,
            ['base_uri' => self::BASE_URI]
        );
    }

    public function post($url, array $data = [])
    {
        return $this->guzzle->request(
            'POST',
            $url,
            [
                'base_uri' => self::BASE_URI,
                'form_params' => $data
            ]
        );
    }

    public function delete($url, array $data = [])
    {
        return $this->guzzle->request(
            'DELETE',
            $url,
            [
                'base_uri' => self::BASE_URI,
                'json' => $data,
            ]
        );
    }
}
