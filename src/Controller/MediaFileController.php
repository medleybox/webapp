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
        $files = [];
        foreach ($this->media->findAll() as $media) {
            $files[] = [
                'uuid' => $media->getUuid(),
                'thumbnail' => $this->media->getThumbnail($media),
                'steam' => $this->media->getStream($media),
                'title' => $media->getTitle(),
                'seconds' => $media->getTitle(),
            ];
        }

        return $this->json([['files' => $files]]);
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
            exit();
        }

        $media = $media[0];

        $media->setType($request->request->get('provider'));
        $media->setTitle($request->request->get('title'));
        $media->setSeconds($request->request->get('seconds'));
        $media->setSize($request->request->get('size'));

        $this->media->save($media);

        return $this->json(['update' => true]);
    }
}
