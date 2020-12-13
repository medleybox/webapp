<?php

namespace App\Controller;

use App\Repository\MediaFileRepository;
use App\Entity\MediaFile;
use App\Form\MediaFileType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{
    /**
     * @var \App\Repository\MediaFileRepository
     */
    private $media;

    public function __construct(MediaFileRepository $media)
    {
        $this->media = $media;
    }

    /**
     * @Route("/player", name="player_index", methods={"GET"})
     */
    public function index(Request $request)
    {
        return $this->render('player/index.html.twig', [
            'files' => $this->media->list()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="player_edit", methods={"GET", "POST"})
     * @ParamConverter("id", class="App\Entity\MediaFile")
     */
    public function edit(MediaFile $media, Request $request)
    {
        $form = $this->createForm(MediaFileType::class, $media);

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));

            if ($form->isSubmitted() && $form->isValid()) {
                $this->media->save($form->getData());

                return $this->redirectToRoute('player_index');
            }
        }

        return $this->render('player/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
