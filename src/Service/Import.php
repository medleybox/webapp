<?php

namespace App\Service;

use App\Entity\MediaFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;

class Import
{
    protected $request;

    protected $em;

    public function __construct(Request $request, EntityManagerInterface $em)
    {
        $this->request = $request;
        $this->em = $em;
    }

    public function processForm(Form $form)
    {
        $data = $form->getData();
        $url = $data['url'];

        $this->import($url);
    }

    public function import($url)
    {
        $response = $this->request->post(
            'entry/import',
            ['id' => $url]
        );

        $data = json_decode($response->getBody(), true);
        dump($response->getBody() . '');
        dump($data);

        $file = new MediaFile;

        $file->setType('youtube');
        $file->setUuid($data['uuid']);

        // Set the title to the URL of import
        $file->setTitle($url);

        $this->em->persist($file);
        $this->em->flush();

        return true;
    }

    public function delete(string $url)
    {
        try {
            $response = $this->request->delete(
                $url
            );

            dump($response->getBody() . '');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
