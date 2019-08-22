<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index_index")
     */
    public function index()
    {
        $number = random_int(0, 100);

        return $this->render('index.html.twig', ['number' => $number]);
    }
}
