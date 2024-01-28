<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\{HealthCheck, Import, Request as Vault};
use App\Entity\MediaFile;
use App\Repository\MediaFileRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\{Annotation\Route, Requirement\Requirement};
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\SecurityBundle\Security;
use Exception;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->redirectToRoute('admin_about');
    }

    #[Route('/admin/about', name: 'admin_about', methods: ['GET'])]
    public function about(Request $request): Response
    {
        return $this->render('admin/about.html.twig');
    }

    #[Route('/admin/about/json', name: 'admin_about_json', methods: ['GET'])]
    public function aboutJson(Request $request, Vault $vault, MediaFileRepository $media, HealthCheck $hc): Response
    {
        $vaultVersion = [];
        $version = $vault->get('api/version');
        if (null !== $version) {
            $vaultVersion = $version->toArray();
        }

        return $this->json([
            'webapp' => $hc->meilisearchVersion() + [
                'symfony' => Kernel::VERSION,
                'php' => PHP_VERSION,
                'files' => $media->count([])
            ],
            'vault' => $vaultVersion
        ]);
    }

    #[Route('/admin/media', name: 'admin_media', methods: ['GET'])]
    public function media(Request $request): Response
    {
        return $this->render('admin/media.html.twig');
    }

    #[Route('/admin/media/refresh-source/{uuid}', name: 'admin_refreshSource', requirements: ['uuid' => Requirement::UUID_V4], methods: ['GET'])]
    public function refreshSource(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] MediaFile $media,
        Vault $vault
    ): Response {
        $json = $vault->get("entry/refresh-source/{$media->getUuid()}")->toArray();

        return $this->json($json);
    }

    #[Route('/admin/media/force-import', name: 'admin_forceImport', requirements: ['uuid' => Requirement::UUID_V4], methods: ['POST'])]
    public function forceImport(Request $request, Vault $vault): Response
    {
        $uuid = $request->request->get('uuid');
        $json = $vault->get("entry/refresh-source/{$uuid}")->toArray();

        return $this->json($json);
    }

    #[Route('/admin/media/json', name: 'admin_media_json', methods: ['GET'])]
    public function mediaJson(Request $request, Vault $vault, MediaFileRepository $media): Response
    {
        $json = [];
        $vaultMedia = $vault->get('entry/list-all')->toArray();
        foreach ($media->findBy([]) as $row) {
            $import = null;
            $user = $row->getImportUser();
            if (null !== $user) {
                $import = $user->__toString();
            }

            $uuid = $row->getUuid();
            $data = [
                'uuid' => $uuid,
                'title' => $row->getTitle(),
                'size' => $row->getSize(),
                'seconds' => $row->getSeconds(),
                'user' => $import,
                'hasVault' => false,
                'vault' => []
            ];

            if (array_key_exists($uuid, $vaultMedia)) {
                $data['hasVault'] = true;
                $data['vault'] = $vaultMedia[$uuid];

                unset($vaultMedia[$uuid]);
            }

            $json[] = $data;
        }

        return $this->json([
            'media' => $json,
            'vault' => $vaultMedia
        ]);
    }
}
