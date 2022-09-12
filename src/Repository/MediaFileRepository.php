<?php

namespace App\Repository;

use App\Entity\{LocalUser, MediaFile};
use App\Service\{Import, Request};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @method MediaFile[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<MediaFile>
 */
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

    /**
     * @var \Symfony\Component\HttpFoundation\UrlHelper;
     */
    private $urlHelper;

    const LIST_LIMIT = 250;

    public function __construct(ManagerRegistry $registry, Import $import, UrlGeneratorInterface $router, Request $request, UrlHelper $urlHelper)
    {
        $this->import = $import;
        $this->router = $router;
        $this->request = $request;
        $this->urlHelper = $urlHelper;

        parent::__construct($registry, MediaFile::class);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function list(): array
    {
        $files = [];
        foreach ($this->findBy([], ['id' => 'DESC'], self::LIST_LIMIT) as $media) {
            // Hide items until they've been imported
            if (null === $media->getSize()) {
                continue;
            }

            $files[] = $this->getApiValue($media);
        }

        return $files;
    }

    public function forUser(LocalUser $user): array
    {
        $files = [];
        foreach ($this->findBy(['importUser' => $user->getId()], ['id' => 'DESC'], self::LIST_LIMIT) as $media) {
            // Hide items until they've been imported
            if (null === $media->getSize()) {
                continue;
            }

            $files[] = $this->getApiValue($media);
        }

        return $files;
    }

    /**
     * @return array<string, string>
     */
    private function getApiValue(MediaFile $media): array
    {
        return [
            'uuid' => $media->getUuid(),
            'thumbnail' => $this->getThumbnail($media),
            'stream' => $this->getStream($media),
            'download' => $this->getDownload($media),
            'metadata' => $this->getMetadataUrl($media),
            'title' => $media->getTitle(),
            'seconds' => $media->getSeconds()
        ];
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

        $metadata = $response->toArray();
        $metadata['size'] = $media->getSize();

        return $metadata;
    }

    /**
     * @return array<string, string>
     */
    public function getWavedata(MediaFile $media): array
    {
        try {
            $response = $this->request->get("entry/wavedata/{$media->getUuid()}");
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
        $this->request->refreshMediaList();

        return true;
    }

    public function delete(MediaFile $media): bool
    {
        // Remove the file from storage
        $this->import->delete($this->getDelete($media));

        $this->_em->remove($media);
        $this->_em->flush();
        $this->request->refreshMediaList();

        return true;
    }

    /**
     * Used to fake a url with media name for stream url
     */
    private function getFakeFilename(MediaFile $media): string
    {
        $name = str_replace(" - ", "", $media->getTitle());

        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    public function getDeleteUrl(MediaFile $media): string
    {
        return $this->router->generate('media_delete', ['uuid' => $media->getUuid()]);
    }

    public function getThumbnail(MediaFile $media): string
    {
        $url = "/vault/entry/thumbnail/{$media->getUuid()}.jpg";

        return $this->urlHelper->getAbsoluteUrl($url);
    }

    public function getStream(MediaFile $media): string
    {
        $url = "/vault/entry/stream/{$media->getUuid()}/{$this->getFakeFilename($media)}";

        return $this->urlHelper->getAbsoluteUrl($url);
    }

    public function getDownload(MediaFile $media): string
    {
        $url = "/vault/entry/download/{$media->getUuid()}";

        return $this->urlHelper->getAbsoluteUrl($url);
    }

    public function getMetadataUrl(MediaFile $media): string
    {
        $url = "/media-file/metadata/{$media->getUuid()}";

        return $this->urlHelper->getAbsoluteUrl($url);
    }

    public function getDelete(MediaFile $media): string
    {
        $url = "entry/delete/{$media->getUuid()}";

        return $this->urlHelper->getAbsoluteUrl($url);
    }
}
