<?php

namespace Tests\AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserbarServiceTest extends KernelTestCase
{
    public static function setUpBeforeClass()
    {
        static::bootKernel();
    }

    public function testGetImage()
    {
        $container = static::$kernel->getContainer();
        $userbar   = $container->get('userbar');
        $em        = $container->get('doctrine.orm.entity_manager');
        $webPath   = $container->getParameter('web_path');
        $user      = $em->getRepository('AppBundle:User')->findOneBy([]);
        $image     = $userbar->getImage($user);

        $this->assertEquals($webPath .'/userbars/'. $user->getHash() .'.png', $image);
    }
}
