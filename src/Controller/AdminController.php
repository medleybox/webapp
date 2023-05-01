<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\{Import, Request as Vault};
use App\Entity\MediaFile;
use App\Repository\MediaFileRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
#use Symfony\Component\Routing\{Annotation\Route, Requirement\Requirement};
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
    public function aboutJson(Request $request, Vault $vault, MediaFileRepository $media): Response
    {
        $vaultVersion = [];
        $version = $vault->get('api/version');
        if (null !== $version) {
            $vaultVersion = $version->toArray();
        }

        return $this->json([
            'webapp' => [
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

    // requirements: ['uuid' => Requirement::UUID_V4])]
    #[Route('/admin/media/refresh-source/{uuid}', name: 'admin_refreshSource', methods: ['GET'])]
    #[ParamConverter('uuid', class: '\App\Entity\MediaFile', options: ['mapping' => ['uuid' => 'uuid']])]
    public function refreshSource(MediaFile $media, Vault $vault): Response
    {
        $json = $vault->get("entry/refresh-source/{$media->getUuid()}")->toArray();

        return $this->json($json);
    }

    #[Route('/admin/media/json', name: 'admin_media_json', methods: ['GET'])]
    public function mediaJson(Request $request, Vault $vault, MediaFileRepository $media): Response
    {
        $vaultMedia = $vault->get('entry/list-all')->toArray();
        $json = [];

        foreach ($media->findBy([]) as $row) {
            $import = null;
            if (null !== $row->getImportUser()) {
                $user = $row->getImportUser();
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
            }

            $json[] = $data;
        }

        return $this->json([
            'media' => $json
        ]);
    }
}
