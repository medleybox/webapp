<?php

namespace App\Repository;

use App\Entity\{LocalUser, UserPasswordReset};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserPasswordReset|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPasswordReset|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPasswordReset[]    findAll()
 * @method UserPasswordReset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<UserPasswordReset>
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
