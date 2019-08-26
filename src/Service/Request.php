<?php

namespace App\Service;

use Symfony\Component\Form\Form;

use Xigen\Bundle\GuzzleBundle\Service\GuzzleClient;

class Request
{
    const BASE_URI = 'http://import';
    //const BASE_URI = 'http://10.10.1.61:8084';

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
            ['base_uri' => SELF::BASE_URI]
        );
    }

    public function post($url, array $data = [])
    {
        return $this->guzzle->request(
            'POST',
            $url,
            [
                'base_uri' => SELF::BASE_URI,
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
                'base_uri' => SELF::BASE_URI,
                'json' => $data,
            ]
        );
    }
}