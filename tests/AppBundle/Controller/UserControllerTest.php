<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testLogout()
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/logout');

        $this->assertTrue($client->getResponse()->isRedirect('/'));
    }

    public function testUser()
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/user');

        $this->assertTrue($client->getResponse()->isRedirect('/'));
    }

    public function testProgress()
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', '/user/progress');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
