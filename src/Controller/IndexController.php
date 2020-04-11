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
     * @Route("/delete", name="index_delete", methods={"POST", "DELETE"})
     */
    public function delete(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $path = $data['path'];
        $delete = $this->import->delete($path);

        return $this->json([
            'delete' => $delete,
            'path' => $path
        ]);
    }

    /**
     * @Route("/", name="index_index")
     */
    public function index(Request $request)
    {
        $number = random_int(0, 100);
        $list = $this->import->list();
        $form = $this->createForm(ImportType::class);

        return $this->render('index.html.twig', [
            'number' => $number,
            'form' => $form->createView(),
            'youtube' => $list['youtube']
        ]);
    }
    
    /**
     * @Route("/add", name="index_add")
     */
    public function add(Request $request)
    {
        $form = $this->createForm(ImportType::class);

        return $this->render('add.html.twig', [
            'form' => $form->createView()
        ]);
    }
    

    /**
     * @Route("/import-form", name="index_import", methods={"POST"})
     */
    public function import(Request $request)
    {
        $form = $this->createForm(ImportType::class);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));

            if ($form->isSubmitted() && $form->isValid()) {
                $inport = $this->import->processForm($form);
                
                return $this->redirectToRoute('index_index');
            }
        }
    }
}
