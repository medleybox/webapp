<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\{LocalUser, UserPasswordReset};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPasswordReset>
 */
class UserPasswordResetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPasswordReset::class);
    }

    public function cleanForUser(LocalUser $user): bool
    {
        $resets = $this->findBy(['localuser' => $user]);
        foreach ($resets as $reset) {
            $this->_em->remove($reset);
        }
        $this->_em->flush();

        return true;
    }
}
