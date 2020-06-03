<?php

namespace App\Repository;

use App\Entity\MediaFile;
use App\Service\Import;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class MediaFileRepository extends ServiceEntityRepository
{
    protected $import;

    public function __construct(ManagerRegistry $registry, Import $import)
    {
        $this->import = $import;
        parent::__construct($registry, MediaFile::class);
    }

    public function save(MediaFile $media)
    {
        if (null === $media->getId()) {
            $this->_em->persist($media);
        }
        $this->_em->flush();

        return true;
    }

    public function delete(MediaFile $media)
    {
        // Remove the file from storage
        $this->import->delete($media->getPath());

        $this->_em->remove($media);
        $this->_em->flush();

        return true;
    }

    public function getThumbnail(MediaFile $media): string
    {
        return "/vault/entry/thumbnail/{$media->getUuid()}";
    }
}
