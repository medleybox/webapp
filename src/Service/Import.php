<?php

namespace App\Service;

use Symfony\Component\Form\Form;

class Import
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
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

        return json_decode($response->getBody(), true);
    }

    public function delete($path)
    {
        $response = $this->request->delete(
            'api/delete/delete',
            ['path' => $path]
        );

        dump($response->getBody() . '');
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