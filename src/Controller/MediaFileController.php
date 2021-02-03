<?php

namespace App\Controller;

use App\Repository\MediaFileRepository;
use App\Entity\MediaFile;
use App\Form\MediaFileType;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MediaFileController extends AbstractController
{
    /**
     * @var \App\Repository\MediaFileRepository
     */
    private $media;

    /**
     * @var \Psr\Log\\LoggerInterface
     */
    private $log;


    public function __construct(MediaFileRepository $media, LoggerInterface $log)
    {
        $this->media = $media;
        $this->log = $log;
    }

    /**
     * @Route("/media-file/list", name="media_list", methods={"GET"})
     */
    public function list(Request $request)
    {
        return $this->json(['files' => $this->media->list()]);
    }

    /**
     * @Route("/media-file/metadata/{uuid}", name="media_metadata", methods={"GET"})
     * @ParamConverter("uuid", class="\App\Entity\MediaFile", options={"mapping": {"uuid": "uuid"}})
     */
    public function metadata(MediaFile $media, Request $request)
    {
        return $this->json(['metadata' => $this->media->getMetadata($media)]);
    }

    /**
     * @Route("/media-file/delete/{uuid}", name="media_delete", methods={"GET", "DELETE"})
     * @ParamConverter("uuid", class="\App\Entity\MediaFile", options={"mapping": {"uuid": "uuid"}})
     */
    public function delete(MediaFile $media, Request $request)
    {
        $this->media->delete($media);

        return $this->json([
            'delete' => true
        ]);
    }

    /**
     * @Route("/media-file/update", name="media_update", methods={"POST"})
     */
    public function update(Request $request)
    {
        $uuid = $request->request->get('uuid');
        if (null === $uuid) {
            exit();
        }

        $media = $this->media->findBy(['uuid' => $uuid]);
        if ([] === $media) {
            $media = [(new MediaFile())->setUuid($uuid)];
        }

        $media = $media[0];
        if (null !== $request->request->get('provider')) {
            $media->setType($request->request->get('provider'));
        }

        if (null !== $request->request->get('title')) {
            $media->setTitle($request->request->get('title'));
        }

        if (null !== $request->request->get('seconds')) {
            $media->setSeconds($request->request->get('seconds'));
        }

        if (null !== $request->request->get('size')) {
            $media->setSize($request->request->get('size'));
        }

        if (null !== $request->request->get('size')) {
            $media->setSize($request->request->get('size'));
        }

        $this->media->save($media);

        return $this->json(['update' => true]);
    }
}
