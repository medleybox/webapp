<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\{AssetHash, Import};
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};
use Symfony\Component\Security\Core\Security;
use Exception;

class IndexController extends AbstractController
{
    public function __construct(private Import $import)
    {
    }

    #[Route(['/','/about','/profile'], name: 'index_index', methods: ['GET'])]
    public function index(Request $request, AssetHash $asset): Response
    {
        return $this->render('index.html.twig', [
            'asset_hash' => $asset->get()
        ]);
    }

    #[Route('/check', name: 'index_check', methods: ['POST'])]
    public function check(Request $request): JsonResponse
    {
        try {
            $check = $this->import->check($request->request->get('url'));
        } catch (\Exception $e) {
            return $this->checkReturn($e->getMessage());
        }

        if (null === $check) {
            return $this->checkReturn('Failed to check import');
        }

        if (false === $check['found']) {
            return $this->checkReturn($check['message']);
        }

        return $this->checkReturn('', true, $check);
    }

    private function checkReturn(string $message = '', ?bool $check = false, ?ArrayCollection $metadata = null): JsonResponse
    {
        return $this->json([
            'message' => $message,
            'check' => $check,
            'metadata' => $metadata ? $metadata->toArray() : ''
        ]);
    }

    #[Route('/import-form', name: 'index_import', methods: ['POST'])]
    public function import(Request $request, Security $security): JsonResponse
    {
        $url = $request->request->get('url', '');
        $uuid = $request->request->get('uuid');
        $title = $request->request->get('title');

        if (null === $uuid) {
            return $this->importReturn();
        }

        try {
            /**
             * null or UserInterface if request has valid session
             * @var \App\Entity\LocalUser
             */
            $user = $security->getUser();
            $import = $this->import->import($uuid, $url, $title, $user);
        } catch (\Exception $e) {
            return $this->importReturn(false, true, $e->getMessage());
        }

        return $this->importReturn($import, true);
    }

    private function importReturn(?bool $import = false, bool $attempt = false, string $error = ''): JsonResponse
    {
        return $this->json([
            'import' => $import,
            'attempt' => $attempt,
            'error' => $error
        ]);
    }
}
