<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\LocalUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserAvatar
{
    public function __construct(
        private Request $request,
        private EntityManagerInterface $em
    ) {
    }

    public function updateUserAvatar(LocalUser $user, UploadedFile $file): bool
    {
        $upload = $this->request->uploadAvatar($file->getPathname(), $file->getMimeType());
        $user->setAvatar($upload);

        $this->em->flush();

        return true;
    }
}
