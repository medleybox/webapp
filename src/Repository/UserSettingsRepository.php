<?php

namespace App\Repository;

use App\Entity\{LocalUser, UserSettings};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserSettings>
 *
 * @method UserSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSettings[]    findAll()
 * @method UserSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSettings::class);
    }

    public function save(UserSettings $settings, LocalUser $user): bool
    {
        if (null === $settings->getId()) {
            $this->getEntityManager()->persist($settings);
            $user->setSettings($settings);
        }
        $this->getEntityManager()->flush();

        return true;
    }
}
