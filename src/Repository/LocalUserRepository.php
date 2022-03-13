<?php

namespace App\Repository;

use App\Entity\LocalUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LocalUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocalUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocalUser[]    findAll()
 * @method LocalUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<LocalUser>
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
