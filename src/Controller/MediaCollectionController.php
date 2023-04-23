<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\{MediaFileRepository, MediaCollectionRepository};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};

class MediaCollectionController extends AbstractController
{
    public function __construct(private MediaCollectionRepository $repo)
    {
    }

    #[Route('/media-collection/index', name: 'mediacollection_index', methods: ['GET'])]
    public function index(Request $request, MediaFileRepository $media): JsonResponse
    {
        return $this->json([
            'tracks' => $media->latest()
        ]);
    }
}
