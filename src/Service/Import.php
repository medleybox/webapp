<?php

namespace App\Service;

use App\Entity\{MediaFile, LocalUser};
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpClient\Exception\ServerException;
use Exception;

class Import
{
    /**
     * @var \App\Service\Request
     */
    protected $request;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    public function __construct(Request $request, EntityManagerInterface $em)
    {
        $this->request = $request;
        $this->em = $em;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function check(string $url): ?ArrayCollection
    {
        $response = $this->request->post(
            'entry/check',
            ['id' => $url]
        );

        $data = $response->toArray();
        if (array_key_exists('found', $data) && false === $data['found']) {
            throw new \Exception($data['message']);
        }

        if (!array_key_exists('uuid', $data)) {
            return null;
        }

        return new ArrayCollection($data);
    }

    public function import(string $uuid, string $url, string $title = null, LocalUser $user = null): ?bool
    {
        try {
            $response = $this->request->post(
                'entry/import',
                [
                    'uuid' => $uuid,
                    'url' => $url,
                ]
            );
        } catch (ServerException $e) {
            throw new \Exception('Unable send request to vault');
        }

        $data = json_decode($response->getContent(), true);
        if (null === $data) {
            throw new \Exception('Unable to decode json response');
        }

        if (true === $data['error']) {
            throw new \Exception($data['message']);
        }

        if (!array_key_exists('uuid', $data)) {
            throw new \Exception('Entry uuid not found');
        }

        $file = new MediaFile();
        $file->setType('youtube');
        $file->setUuid($data['uuid']);

        // Set the title to the URL of import if set
        if (null !== $title) {
            $file->setTitle($title);
        }

        if (null !== $user) {
            $file->setImportUser($user);
        }

        $this->em->persist($file);
        $this->em->flush();

        return true;
    }

    public function delete(string $url): bool
    {
        try {
            $this->request->delete($url);
            return true;
        } catch (\Exception $e) {
            //
        }

        return false;
    }
}
