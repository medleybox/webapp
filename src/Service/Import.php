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
            'api/import/youtube',
            ['url' => $url]
        );

        $data = json_decode($response->getBody(), true);
        dump($response->getBody() . '');
        dump($data);

        $file = new MediaFile;

        $file->setType('youtube');
        $file->setSize($data['size']);
        $file->setSeconds($data['seconds']);
        $file->setPath($data['path']);

        $this->em->persist($file);
        $this->em->flush();

        return true;
    }

    public function delete($path)
    {
        try {
            $response = $this->request->delete(
                'api/delete/delete',
                ['path' => $path]
            );

            dump($response->getBody() . '');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function list()
    {
        $response = $this->request->get(
            'api/list/all',
            ['url' => $url]
        );

        return json_decode($response->getBody(), true);
    }
}
