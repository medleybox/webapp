<?php

namespace App\Controller;

use App\Service\HealthCheck;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Kernel;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/version", name="api_version")
     */
    public function version(): Response
    {
        return $this->json([
            'symfony' => Kernel::VERSION
        ]);
    }

    /**
     * @Route("/api/healthcheck", name="api_healthcheck")
     */
    public function healthcheck(HealthCheck $healthcheck): Response
    {
        return $this->json($healthcheck->getOverview());
    }
}
