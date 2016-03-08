<?php

namespace Tests\AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\User;
use AppBundle\Entity\Game;
use AppBundle\Entity\UserGame;
use AppBundle\Entity\UserAchievement;
use AppBundle\Entity\GameAchievement;

class SteamDataServiceTest extends KernelTestCase
{
    const STEAMID = 123456789;
    const GAMEID  = -1;
    const KEY     = 'STEAMETER_TEST';

    private static $steamData;

    public static function setUpBeforeClass()
    {
        static::bootKernel();

        static::$steamData = static::$kernel->getContainer()->get('steam_data');
    }

    public function testGetUser()
    {
        $this->assertInstanceOf(
            User::class,
            static::$steamData->getUser(static::STEAMID, false)
        );
    }

    public function testGetGame()
    {
        $this->assertInstanceOf(
            Game::class,
            static::$steamData->getGame(static::GAMEID, false)
        );
    }

    public function testGetUserGame()
    {
        $this->assertInstanceOf(
            UserGame::class,
            static::$steamData->getUserGame(static::STEAMID, static::GAMEID, false)
        );
    }

    public function testGetGameAchievement()
    {
        $this->assertInstanceOf(
            GameAchievement::class,
            static::$steamData->getGameAchievement(static::GAMEID, static::KEY, false)
        );
    }

    public function testGetUserAchievement()
    {
        $this->assertInstanceOf(
            UserAchievement::class,
            static::$steamData->getUserAchievement(static::STEAMID, static::GAMEID, static::KEY, false)
        );
    }

    public function testGetRating()
    {
        $user = static::$steamData->getUser(static::STEAMID, false);

        $this->assertNull(static::$steamData->getRating($user));
    }

    public function testGetStatistics()
    {
        $user = static::$steamData->getUser(static::STEAMID, false);

        $data = static::$steamData->getStatistics($user);

        $this->assertInternalType('array', $data);

        $this->assertArrayHasKey('games', $data);
        $this->assertArrayHasKey('hours', $data);
        $this->assertArrayHasKey('achievements', $data);
    }

    public function testGetUserProgress()
    {
        $user = static::$steamData->getUser(static::STEAMID, false);

        $data = static::$steamData->getUserProgress($user);

        $this->assertEquals([
            'in_progress' => false,
            'status'      => 'finished',
            'percentage'  => 0
        ], $data);

        $user->setIsBeingHandled(true);

        $data = static::$steamData->getUserProgress($user);

        $this->assertEquals([
            'in_progress' => true,
            'status'      => 'games',
            'percentage'  => 0
        ], $data);
    }
}
