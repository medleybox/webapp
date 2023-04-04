<?php

namespace App\Service;

use App\Repository\{LocalUserRepository};
use App\Entity\LocalUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserAvatar
{
    /**
     * @var \App\Service\Request
     */
    protected $request;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \App\Repository\LocalUserRepository
     */
    private LocalUserRepository $users;

    public function __construct(Request $request, EntityManagerInterface $em, LocalUserRepository $users)
    {
        $this->request = $request;
        $this->em = $em;
        $this->users = $users;
    }

    public function updateUserAvatar(LocalUser $user, UploadedFile $file): bool
    {
        $upload = $this->request->uploadAvatar($file->getPathname(), $file->getMimeType());
        $user->setAvatar($upload);

        $this->em->flush();

        return true;
    }

    public function fetchUserAvatar(LocalUser $user)
    {
        $avatar = $user->getAvatar();
        if (null === $avatar) {
            return null;
        }
        $download = $this->request->fetchAvatar($avatar);

        dump($download);
        return $download;
    }
}
