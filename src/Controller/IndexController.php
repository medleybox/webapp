<?php

namespace App\Controller;

use App\Service\Import;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Security\Core\Security;
use Exception;

class IndexController extends AbstractController
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
     * @Route("/", name="index_index")
     * @Route("/about")
     */
    public function index(Request $request): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/check", name="index_check", methods={"POST"})
     */
    public function check(Request $request): Response
    {
        try {
            $check = $this->import->check($request->request->get('url'));
        } catch (\Exception $e) {
            return $this->json(['check' => false, 'message' => $e->getMessage()]);
        }

        if (null === $check) {
            return $this->json([
                'check' => false,
                'message' => 'Failed to check import'
            ]);
        }

        if (false === $check['found']) {
            return $this->json([
                'check' => false,
                'message' => $check['message']
            ]);
        }

        return $this->json([
            'check' => true,
            'metadata' => $check
        ]);
    }

    /**
     * @Route("/import-form", name="index_import", methods={"POST"})
     */
    public function import(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $url = $request->request->get('url', '');
            $uuid = $request->request->get('uuid');
            $title = $request->request->get('title');

            if (null === $uuid) {
                return $this->json(['import' => false, 'attempt' => false]);
            }

            try {
                /**
                 * null or UserInterface if request has valid session
                 * @var \App\Entity\LocalUser
                 */
                $user = $this->security->getUser();
                $import = $this->import->import($uuid, $url, $title, $user);
            } catch (\Exception $e) {
                return $this->json(['import' => false, 'attempt' => true, 'error' => $e->getMessage()]);
            }

            return $this->json(['import' => $import, 'attempt' => true]);
        }

        return $this->json(['import' => false, 'attempt' => true]);
    }
}
