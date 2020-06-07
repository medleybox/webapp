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
     * @Route("/import-form", name="index_import", methods={"POST"})
     */
    public function import(Request $request)
    {
        if ($request->isMethod('POST')) {
            $url = $request->request->get('url');
            if (null === $url) {
                exit();
            }
            $inport = $this->import->import($url);

            return $this->json(['import' => $inport, 'attepmt' => true]);
        }

        return $this->json(['import' => false, 'attepmt' => true]);
    }
}
