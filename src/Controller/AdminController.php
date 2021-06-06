<?php

namespace App\Controller;

use App\Service\Import;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Security;
use Exception;

class AdminController extends AbstractController
{
    /**
     * @var \App\Service\Import
     */
    private $import;

    /**
     * @var \Symfony\Component\Security\Core\Security
     */
    private $security;

    public function __construct(Import $import, Security $security)
    {
        $this->import = $import;
        $this->security = $security;
    }

    /**
     * @Route("/admin", name="admin_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        return $this->redirectToRoute('admin_about');
    }

    /**
     * @Route("/admin/about", name="admin_about", methods={"GET"})
     */
    public function about(Request $request): Response
    {
        return $this->render('admin/about.html.twig');
    }

    /**
     * @Route("/admin/about/json", name="admin_about_json", methods={"GET"})
     */
    public function aboutJson(Request $request): Response
    {
        return $this->json([
            'webapp' => [
                'symfony' => Kernel::VERSION,
                'php' => PHP_VERSION
            ]
        ]);
    }
}
