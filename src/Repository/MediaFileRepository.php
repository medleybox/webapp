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

    private function getFakeFilename(MediaFile $media): string
    {
        $name = str_replace(" - ", "", $media->getTitle());

        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    public function getThumbnail(MediaFile $media): string
    {
        return "/vault/entry/thumbnail/{$media->getUuid()}";
    }

    public function getStream(MediaFile $media): string
    {
        return "/vault/entry/steam/{$media->getUuid()}/{$this->getFakeFilename($media)}";
    }
}
