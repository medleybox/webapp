<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LocalUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LocalUser>
 */
class LocalUserRepository extends ServiceEntityRepository
{
    /**
     * @var \App\Repository\MediaFileRepository
     */
    protected $media;

    /**
     * @var \App\Repository\UserPasswordResetRepository
     */
    protected $reset;

    public function __construct(ManagerRegistry $registry, MediaFileRepository $media, UserPasswordResetRepository $reset)
    {
        parent::__construct($registry, LocalUser::class);
        $this->media = $media;
        $this->reset = $reset;
    }

    public function getDefaultUser(): ?LocalUser
    {
        foreach ($this->findBy([], ['id' => 'ASC'], 10) as $user) {
            if ($user->hasRole('ROLE_ADMIN')) {
                return $user;
            }
        }

        return null;
    }

    public function save(LocalUser $user): bool
    {
        if (null === $user->getId()) {
            $this->_em->persist($user);
        }
        $this->_em->flush();

        return true;
    }

    public function delete(LocalUser $user): bool
    {
        if (null !== $user->getId()) {
            $this->removeUserMedia($user);
            $this->removePasswordResets($user);
            $this->_em->remove($user);
        }
        $this->_em->flush();

        return true;
    }

    private function removeUserMedia(LocalUser $user): bool
    {
        foreach ($user->getMediaFiles() as $file) {
            $this->media->delete($file);
        }

        return true;
    }

    private function removePasswordResets(LocalUser $user): bool
    {
        $this->reset->cleanForUser($user);

        return true;
    }
}
