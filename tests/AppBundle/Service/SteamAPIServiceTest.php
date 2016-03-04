<?php

namespace Tests\AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Exception\SteamResponseException;

class SteamAPIServiceTest extends KernelTestCase
{
    const STEAMID = '76561197960435530';
    const GAMEID  = 440;

    private static $steamApi;

    public static function setUpBeforeClass()
    {
        static::bootKernel();

        static::$steamApi = static::$kernel->getContainer()->get('steam_api');
    }

    public function testGetUserData()
    {
        $data = static::$steamApi->getUserData(static::STEAMID);

        $this->assertInternalType('array', $data);

        $this->assertArrayHasKey('personaname', $data);
    }

    public function testGetUserGames()
    {
        $this->assertInternalType(
            'array',
            static::$steamApi->getUserGames(static::STEAMID)
        );
    }

    public function testGetUserAchievements()
    {
        $data = static::$steamApi->getUserAchievements(static::STEAMID, static::GAMEID);

        $this->assertInternalType('array', $data);

        $this->assertGreaterThan(0, count($data));
    }

    public function testGetGameAchievements()
    {
        $data = static::$steamApi->getGameAchievements(static::GAMEID);

        $this->assertInternalType('array', $data);

        $this->assertGreaterThan(0, count($data));
    }

    public function testException()
    {
        $this->setExpectedException(SteamResponseException::class);

        static::$steamApi->getUserData(0);
    }
}
