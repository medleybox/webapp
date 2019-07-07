<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class IndexController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        $number = random_int(0, 100);

        return new Response(
            '' . $number
        );
    }
}