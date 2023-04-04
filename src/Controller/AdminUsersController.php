<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\LocalUser;
use App\Repository\LocalUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, Response};

class AdminUsersController extends AbstractController
{
    public function __construct(private LocalUserRepository $repo)
    {
    }

    /**
     * @return array<string, bool|int|string|null>
     */
    private function getUserAsArray(LocalUser $user): array
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'avatar' => $user->getAvatarPath(),
            'email' => $user->getEmail(),
            'active' => $user->getActive(),
            'isAdmin' => $user->hasRole('ROLE_ADMIN'),
        ];
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
        foreach ($this->repo->findBy([], ['id' => 'ASC']) as $user) {
            $users[] = $this->getUserAsArray($user);
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

            $user->removeRole('ROLE_ADMIN');
            if ('true' === $request->request->get('isAdmin', false)) {
                $user->setRole('ROLE_ADMIN');
            }

            $this->repo->save($user);

            return $this->json(['success' => true], Response::HTTP_CREATED);
        }

        return $this->json(['user' => $this->getUserAsArray($user)]);
    }

    /**
     * @Route("/admin/users/delete/{id}", name="admin_users_delete", methods={"DELETE"})
     * @ParamConverter("id", class="\App\Entity\LocalUser")
     */
    public function deleteUser(LocalUser $user, Request $request): Response
    {
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $msg = "Unable to delete admin user!";
            return $this->json(['success' => false, 'msg' => $msg], Response::HTTP_FORBIDDEN);
        }
        $this->repo->delete($user);

        return $this->json(['success' => true], Response::HTTP_OK);
    }
}
