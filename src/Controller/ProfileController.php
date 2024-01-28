<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\{AssetHash, UserAvatar, Import};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Response, Request};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mime\MimeTypes;
use Exception;

class ProfileController extends AbstractController
{
     /**
     * @var \App\Entity\LocalUser
     */
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/profile', name: 'profile_index', methods: ['GET'])]
    public function index(AssetHash $asset): Response
    {
        return $this->render('index.html.twig', [
            'asset_hash' => $asset->get()
        ]);
    }

    #[Route('/profile/avatar', name: 'profile_avatar', methods: ['GET'])]
    public function avatar(Request $request): Response
    {
        $avatar = $this->user->getAvatarPath();
        if (null === $avatar) {
            return new Response(null, 404);
        }

        return $this->redirect($avatar);
    }

    #[Route('/profile/new-avatar', name: 'profile_newAvatar', methods: ['POST'])]
    public function newAvatar(Request $request, UserAvatar $avatar)
    {
        $file = $request->files->get('file');
        if (null === false) {
            throw new BadRequestHttpException();
        }
        $avatar->updateUserAvatar($this->user, $file);

        return $this->json(['completed' => true]);
    }

    #[Route('/profile/data', name: 'profile_data', methods: ['GET'])]
    public function data(Request $request): JsonResponse
    {
        return $this->json([
            'username' => $this->user->getUsername(),
            'avatar' => $this->user->getAvatarPath()
        ]);
    }
}
