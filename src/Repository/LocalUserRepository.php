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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocalUser::class);
    }

    public function save(LocalUser $user): bool
    {
        if (null === $user->getId()) {
            $this->_em->persist($user);
        }
        $this->_em->flush();

        return true;
    }
}
