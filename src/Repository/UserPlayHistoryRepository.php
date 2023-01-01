<?php

namespace App\Repository;

use App\Kernel;
use App\Entity\{LocalUser, MediaFile, UserPlayHistory};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPlayHistory>
 *
 * @method UserPlayHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPlayHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPlayHistory[]    findAll()
 * @method UserPlayHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPlayHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPlayHistory::class);
    }

    public function getAll(LocalUser $user)
    {
        $history = [];
        foreach ($user->getUserPlayHistories() as $row) {
            $history[] = [
                'media' => $row->getMedia()->getUuid(),
                'completed' => $row->getCompleted(),
                'created' => $row->getAdded()->format(Kernel::APP_TIMEFORMAT),
            ];
        }

        return array_reverse($history);
    }

    public function add(MediaFile $media, LocalUser $user): void
    {
        $record = (new UserPlayHistory)->setMedia($media)->setLocalUser($user);
        $this->getEntityManager()->persist($record);

        $this->getEntityManager()->flush();
    }

    public function remove(UserPlayHistory $record): void
    {
        $this->getEntityManager()->remove($record);
    }

    public function clearHistory(LocalUser $user): void
    {
        foreach($user->getUserPlayHistories() as $row) {
            $this->getEntityManager()->remove($row);
        }
        
        $this->getEntityManager()->flush();
    }

    public function update(MediaFile $media, LocalUser $user): bool
    {
        $history = $user->getUserPlayHistories();

        // No history yet so just create the fist entry
        if (false === $history->last()) {
            $this->add($media, $user);

            return true;
        }

        if ($history->last()->getMedia()->getUuid() != $media->getUuid()) {
            $this->add($media, $user);

            return true;
        }

        return false;
    }
}
