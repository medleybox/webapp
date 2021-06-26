<?php

namespace App\Controller;

use App\Entity\LocalUser;
use App\Repository\LocalUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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

    /**
     * @Route("/admin/users/json/{id}", name="admin_users_json_id", methods={"GET", "POST"})
     * @ParamConverter("id", class="\App\Entity\LocalUser")
     */
    public function usersJsonId(LocalUser $user, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $user->setUsername($request->request->get('username'));
            $user->setEmail($request->request->get('email'));

            $this->repo->save($user);

            return $this->json(['success' => true], Response::HTTP_CREATED);
        }

        $user = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'active' => $user->getActive()
        ];

        return $this->json(['user' => $user]);
    }
}
