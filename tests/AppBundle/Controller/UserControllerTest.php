<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class UserControllerTest extends WebTestCase
{
    private $client;

    private $container;

    private $em;

    private $user;

    protected function setUp()
    {
        $this->client    = static::createClient();
        $this->container = $this->client->getContainer();
        $this->em        = $this->container->get('doctrine.orm.entity_manager');
    }

    protected function tearDown()
    {
        if (null === $this->user) {
            return;
        }

        $this->em
            ->createQuery('DELETE AppBundle:User u WHERE u.steamid = :steamid')
            ->setParameter('steamid', 123456789)
            ->execute()
        ;
    }

    private function logIn()
    {
        $session = $this->container->get('session');

        $this->user = $this->container->get('steam_data')->getUser(123456789, false);
        $this->user->setIsBeingHandled(true);
        $this->em->persist($this->user);
        $this->em->flush();

        $token = new UsernamePasswordToken($this->user, null, 'main', $this->user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    public function testUser()
    {
        $crawler = $this->client->request('GET', '/user');

        $this->assertTrue($this->client->getResponse()->isRedirect('/'));

        $this->logIn();

        $crawler = $this->client->request('GET', '/user');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertContains('Updating the game list', $this->client->getResponse()->getContent());
    }

    public function testProgress()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/user/progress');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertContains('Updating the game list', $this->client->getResponse()->getContent());

        $this->client->restart();

        $crawler = $this->client->request('GET', '/user/progress');

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testLogout()
    {
        $crawler = $this->client->request('GET', '/logout');

        $this->assertTrue($this->client->getResponse()->isRedirect('/'));
    }
}
