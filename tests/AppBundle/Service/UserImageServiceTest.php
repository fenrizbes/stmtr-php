<?php

namespace Tests\AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserImageServiceTest extends KernelTestCase
{
    private static $container;

    private static $userImage;

    private static $em;

    private static $webPath;

    private static $user;

    public static function setUpBeforeClass()
    {
        static::bootKernel();

        static::$container = static::$kernel->getContainer();
        static::$userImage = static::$container->get('user_image');
        static::$em        = static::$container->get('doctrine.orm.entity_manager');
        static::$webPath   = static::$container->getParameter('web_path');
        static::$user      = static::$em->getRepository('AppBundle:User')->findOneBy([]);
    }

    public function testGetUserbar()
    {
        $image = static::$userImage->getUserbar(static::$user);

        $this->assertEquals(static::$webPath .'/userbars/'. static::$user->getHash() .'.png', $image);
    }

    public function testGetShareImage()
    {
        $image = static::$userImage->getShareImage(static::$user);

        $this->assertEquals(static::$webPath .'/sharing/'. static::$user->getHash() .'.png', $image);
    }
}
