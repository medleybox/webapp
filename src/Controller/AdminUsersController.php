<?php

namespace App\Controller;

use App\Repository\LocalUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Security\Core\Security;
use Exception;

class AdminUsersController extends AbstractController
{
    /**
     * @var \App\Repository\LocalUserRepository
     */
    private $repo;

    public function __construct(LocalUserRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @Route("/admin/users", name="admin_users", methods={"GET"})
     */
    public function users(): Response
    {
        return $this->render('admin/users.html.twig');
    }

    /**
     * @Route("/admin/users/json", name="admin_users_json", methods={"GET"})
     */
    public function usersJson(): Response
    {
        $users = [];
        foreach ($this->repo->findBy([]) as $user) {
            $users[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'active' => $user->getActive()
            ];
        }

        return $this->json(['users' => $users]);
    }
}
