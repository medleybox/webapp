<?php

declare(strict_types=1);

namespace App\Repository;

use App\Kernel;
use App\Entity\{LocalUser, MediaFile, UserPlayHistory};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPlayHistory>
 */
class UserPlayHistoryRepository extends ServiceEntityRepository
{
    public function __construct(
        private MediaFileRepository $mediafile,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, UserPlayHistory::class);
    }

    public function getAll(LocalUser $user): array
    {
        $history = [];
        foreach ($user->getUserPlayHistories() as $row) {
            $history[] = [
                'media' => $this->mediafile->getApiValue($row->getMedia()),
                'completed' => $row->getCompleted(),
                'created' => $row->getAdded()->format(Kernel::APP_TIMEFORMAT),
            ];
        }

        return array_reverse($history);
    }

    public function add(MediaFile $media, LocalUser $user): void
    {
        $record = (new UserPlayHistory())->setMedia($media)->setLocalUser($user);
        $this->getEntityManager()->persist($record);

        $this->getEntityManager()->flush();
    }

    public function remove(UserPlayHistory $record): void
    {
        $this->getEntityManager()->remove($record);
    }

    public function clearHistory(LocalUser $user): void
    {
        foreach ($user->getUserPlayHistories() as $row) {
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
