<?php

namespace App\Controller;

use App\Form\ImportType;
use App\Service\Import;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, Response};

class IndexController extends AbstractController
{
    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    /**
     * @Route("/", name="index_index")
     * @Route("/about")
     */
    public function index(Request $request)
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/check", name="index_check", methods={"POST"})
     */
    public function check(Request $request)
    {
        $check = $this->import->check($request->request->get('url'));
        if (false === $check) {
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
    public function import(Request $request)
    {
        if ($request->isMethod('POST')) {
            $uuid = $request->request->get('uuid');
            $url = $request->request->get('url');
            if (null === $uuid || null === $url) {
                return $this->json(['import' => false, 'attepmt' => false]);
            }
            $inport = $this->import->import($uuid, $url);

            return $this->json(['import' => $inport, 'attepmt' => true]);
        }

        return $this->json(['import' => false, 'attepmt' => true]);
    }
}
