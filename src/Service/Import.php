<?php

namespace App\Service;

use App\Entity\MediaFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use GuzzleHttp\Exception\ServerException;
use \Exception;

class Import
{
    protected $request;

    protected $em;

    public function __construct(Request $request, EntityManagerInterface $em)
    {
        $this->request = $request;
        $this->em = $em;
    }

    public function processForm(Form $form): bool
    {
        $data = $form->getData();
        $url = $data['url'];

        return $this->import($url);
    }

    public function check($url)
    {
        $response = $this->request->post(
            'entry/check',
            ['id' => $url]
        );
        $data = json_decode($response->getBody(), true);
        if (array_key_exists('found', $data) && false === $data['found']) {
            throw new \Exception($data['message']);
        }

        if (!array_key_exists('uuid', $data)) {
            return false;
        }

        return $data;
    }

    public function import(string $uuid, string $url, string $title = null): ?bool
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

        $data = json_decode($response->getBody(), true);
        if (null === $data) {
            throw new \Exception('Unable to decode json response');
        }

        if (true === $data['error']) {
            throw new \Exception($data['message']);
        }

        if (!array_key_exists('uuid', $data)) {
            throw new \Exception('Entry uuid not found');
        }

        $file = new MediaFile;
        $file->setType('youtube');
        $file->setUuid($data['uuid']);

        // Set the title to the URL of import if set
        if (null !== $title) {
            $file->setTitle($title);
        }

        $this->em->persist($file);
        $this->em->flush();

        return true;
    }

    public function delete(string $url): bool
    {
        try {
            $response = $this->request->delete($url);
            dump($response->getBody() . '');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
