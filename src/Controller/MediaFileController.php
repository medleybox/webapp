<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\{MediaFileRepository, LocalUserRepository, UserPlayHistoryRepository};
use App\Entity\{MediaFile, LocalUser};
use App\Form\MediaFileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MediaFileController extends AbstractController
{
    public function __construct(private MediaFileRepository $media)
    {
    }

    /**
     * @Route("/media-file/list", name="media_list", methods={"GET"})
     */
    public function list(Security $security): Response
    {
        $user = $security->getUser();
        assert($user instanceof LocalUser);

        return $this->json([
            'files' => [
                'user' => $this->media->forUser($user),
                'latest' => $this->media->latest(),
                'suggested' => $this->media->suggested($user),
            ],
            'user' => [
                'id' => $user->getId()
            ]
        ]);
    }

    /**
     * @Route("/media-file/latest-list", name="media_latest_list", methods={"GET"})
     */
    public function latestList(): Response
    {
        return $this->json($this->media->latest());
    }

    /**
     * @Route("/media-file/suggested-list", name="media_suggested_list", methods={"GET"})
     */
    public function suggested(Security $security): Response
    {
        $user = $security->getUser();
        assert($user instanceof LocalUser);

        return $this->json($this->media->suggested($user));
    }

    /**
     * @Route("/media-file/user-list", name="media_user_list", methods={"GET"})
     */
    public function userList(Security $security): Response
    {
        $user = $security->getUser();
        assert($user instanceof LocalUser);

        return $this->json($this->media->forUser($user));
    }

    /**
     * @Route("/media-file/metadata/{uuid}", name="media_metadata", methods={"GET", "HEAD"})
     * @ParamConverter("uuid", class="\App\Entity\MediaFile", options={"mapping": {"uuid": "uuid"}})
     */
    public function metadata(MediaFile $media): Response
    {
        $importuser = $media->getImportUser();
        if (null !== $importuser) {
            $importuser = $importuser->getUsername();
        }

        return $this->json([
            'loaded' => true,
            'title' => $media->getTitle(),
            'importuser' => $importuser,
            'metadata' => $this->media->getMetadata($media),
            'delete' => $this->media->getDeleteUrl($media),
            'seconds' => $media->getSeconds(),
        ]);
    }

    /**
     * @Route("/media-file/wavedata/{uuid}", name="media_wavedata", methods={"GET"})
     * @ParamConverter("uuid", class="\App\Entity\MediaFile", options={"mapping": {"uuid": "uuid"}})
     */
    public function wavedata(MediaFile $media): Response
    {
        return $this->json($this->media->getWavedata($media));
    }

    /**
     * @Route("/media-file/delete/{uuid}", name="media_delete", methods={"GET", "DELETE"})
     * @ParamConverter("uuid", class="\App\Entity\MediaFile", options={"mapping": {"uuid": "uuid"}})
     */
    public function delete(MediaFile $media): Response
    {
        $this->media->delete($media);

        return $this->json([
            'delete' => true
        ]);
    }

    /**
     * @Route("/media-file/play/{uuid}", name="media_play", methods={"GET", "HEAD"})
     * @ParamConverter("uuid", class="\App\Entity\MediaFile", options={"mapping": {"uuid": "uuid"}})
     */
    public function play(MediaFile $media, Security $security, UserPlayHistoryRepository $history): Response
    {
        $user = $security->getUser();
        assert($user instanceof LocalUser);
        $history->update($media, $user);

        return $this->json([
            'play' => true
        ]);
    }

    /**
     * @Route("/media-file/update", name="media_update", methods={"POST"})
     */
    public function update(Request $request, LocalUserRepository $user): Response
    {
        $uuid = $request->request->get('uuid');
        if (null === $uuid) {
            exit();
        }

        $media = $this->media->findBy(['uuid' => $uuid]);
        if ([] === $media) {
            $media = [(new MediaFile())->setUuid($uuid)];
        }

        $media = $media[0];
        if (null !== $request->request->get('provider')) {
            $media->setType($request->request->get('provider'));
        }

        if (null !== $request->request->get('title')) {
            // Check that media hasn't already been imported and title not set
            if (null !== $media->getSize() || '' === $media->getTitle()) {
                $media->setTitle($request->request->get('title'));
            }
        }

        if (null !== $request->request->get('seconds')) {
            $media->setSeconds((float) $request->request->get('seconds'));
        }

        if (null !== $request->request->get('size')) {
            $media->setSize((int) $request->request->get('size'));
        }

        if (null === $media->getImportUser()) {
            $importUser = $user->getDefaultUser();
            $media->setImportUser($importUser);
        }

        $this->media->save($media);

        return $this->json(['update' => true]);
    }
}
