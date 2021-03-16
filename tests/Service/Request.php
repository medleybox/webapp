<?php

namespace App\Tests\Service;

use App\Service\Request;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RequestTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->request = $kernel->getContainer()->get('App\Service\Request');
        $this->request->setBaseUrl('https://httpbin.org');
    }

    public function testGet()
    {
        $response = $this->request->get('/get');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPost()
    {
        $testData = ['test' => 'data'];
        $response = $this->request->post('/post', $testData);
        $responseData = $response->toArray()['form'];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($testData, $responseData);
    }

    public function testDelete()
    {
        $delete = $this->request->delete('/delete');
        $this->assertTrue($delete);
    }

    public function testDeleteFail()
    {
        $delete = $this->request->delete('/post');
        $this->assertFalse($delete);
    }
}