<?php

namespace App\Repository;

use App\Entity\MediaFile;
use App\Service\{Import, Request};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MediaFileRepository extends ServiceEntityRepository
{
    /**
     * @var \App\Service\Import
     */
    protected $import;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var \App\Service\Request
     */
    protected $request;

    public function __construct(ManagerRegistry $registry, Import $import, UrlGeneratorInterface $router, Request $request)
    {
        $this->import = $import;
        $this->router = $router;
        $this->request = $request;

        parent::__construct($registry, MediaFile::class);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function list(): array
    {
        $files = [];
        foreach ($this->findBy([], ['id' => 'DESC']) as $media) {
            $files[] = [
                'uuid' => $media->getUuid(),
                'thumbnail' => $this->getThumbnail($media),
                'stream' => $this->getStream($media),
                'download' => $this->getDownload($media),
                'metadata' => $this->getMetadataUrl($media),
                'title' => $media->getTitle(),
                'seconds' => $media->getSeconds(),
                'delete' => $this->router->generate('media_delete', ['uuid' => $media->getUuid()])
            ];
        }

        return $files;
    }

    /**
     * @return array<string, string>
     */
    public function getMetadata(MediaFile $media): array
    {
        try {
            $response = $this->request->get("entry/metadata/{$media->getUuid()}");
        } catch (ServerException $e) {
            throw new \Exception('Unable send request to vault');
        }

        return $response->toArray();
    }

    public function save(MediaFile $media): bool
    {
        if (null === $media->getId()) {
            $this->_em->persist($media);
        }
        $this->_em->flush();

        return true;
    }

    public function delete(MediaFile $media): bool
    {
        // Remove the file from storage
        $this->import->delete($this->getDelete($media));

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
        return "/vault/entry/thumbnail/{$media->getUuid()}.jpg";
    }

    public function getStream(MediaFile $media): string
    {
        return "/vault/entry/steam/{$media->getUuid()}/{$this->getFakeFilename($media)}";
    }

    public function getDownload(MediaFile $media): string
    {
        return "/vault/entry/download/{$media->getUuid()}";
    }

    public function getMetadataUrl(MediaFile $media): string
    {
        return "/media-file/metadata/{$media->getUuid()}";
    }

    public function getDelete(MediaFile $media): string
    {
        return "entry/delete/{$media->getUuid()}";
    }
}
