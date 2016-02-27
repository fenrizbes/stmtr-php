<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Game;
use AppBundle\Entity\UserGame;
use AppBundle\Entity\UserAchievement;
use AppBundle\Entity\GameAchievement;

class SteamDataService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var SteamAPIService
     */
    protected $steamApi;

    /**
     * @param EntityManager $em
     * @param SteamAPIService $steamApi
     */
    public function __construct(EntityManager $em, SteamAPIService $steamApi)
    {
        $this->em       = $em;
        $this->steamApi = $steamApi;
    }

    /**
     * Find or create a user by steamid
     *
     * @param int $steamid
     * @param bool $flush
     *
     * @return User
     */
    public function getUser($steamid, $flush = true)
    {
        $user = $this->em->getRepository('AppBundle:User')->find($steamid);
        
        if (!$user instanceof User) {
            $user = new User();
            $user->setSteamid($steamid);

            $this->em->persist($user);

            if ($flush) {
                $this->em->flush();
            }
        }

        return $user;
    }

    /**
     * Find or create a game by gameid
     *
     * @param int $gameid
     * @param bool $flush
     *
     * @return Game
     */
    public function getGame($gameid, $flush = true)
    {
        $game = $this->em->getRepository('AppBundle:Game')->find($gameid);
        
        if (!$game instanceof Game) {
            $game = new Game();
            $game->setGameid($gameid);

            $this->em->persist($game);

            if ($flush) {
                $this->em->flush();
            }
        }

        return $game;
    }

    /**
     * Find or create a user-to-game association
     *
     * @param User|int $user
     * @param Game|int $game
     * @param bool $flush
     *
     * @return UserGame
     */
    public function getUserGame($user, $game, $flush = true)
    {
        if (!$user instanceof User) {
            $user = $this->getUser($user, $flush);
        }

        if (!$game instanceof Game) {
            $game = $this->getGame($game, $flush);
        }

        $userGame = $this->em->getRepository('AppBundle:UserGame')->findOneBy([
            'user' => $user,
            'game' => $game
        ]);
        
        if (!$userGame instanceof UserGame) {
            $userGame = new UserGame();
            $userGame->setUser($user);
            $userGame->setGame($game);

            $this->em->persist($userGame);

            if ($flush) {
                $this->em->flush();
            }
        }

        return $userGame;
    }

    /**
     * Find or create a game achievement
     *
     * @param Game|int $game
     * @param string $key
     * @param bool $flush
     *
     * @return GameAchievement
     */
    public function getGameAchievement($game, $key, $flush = true)
    {
        if (!$game instanceof Game) {
            $game = $this->getGame($game, $flush);
        }

        $gameAchievement = $this->em->getRepository('AppBundle:GameAchievement')->findOneBy([
            'game' => $game,
            'key'  => $key
        ]);
        
        if (!$gameAchievement instanceof GameAchievement) {
            $gameAchievement = new GameAchievement();
            $gameAchievement->setGame($game);
            $gameAchievement->setKey($key);

            $this->em->persist($gameAchievement);

            if ($flush) {
                $this->em->flush();
            }
        }

        return $gameAchievement;
    }

    /**
     * Find or create a user achievement
     *
     * @param User|int $user
     * @param string $key
     * @param bool $flush
     *
     * @return UserAchievement
     */
    public function getUserAchievement($user, $game, $key, $flush = true)
    {
        if (!$user instanceof User) {
            $user = $this->getUser($user, $flush);
        }

        $gameAchievement = $this->getGameAchievement($game, $key, $flush);

        $userAchievement = $this->em->getRepository('AppBundle:UserAchievement')->findOneBy([
            'user'            => $user,
            'gameAchievement' => $gameAchievement
        ]);
        
        if (!$userAchievement instanceof UserAchievement) {
            $userAchievement = new UserAchievement();
            $userAchievement->setUser($user);
            $userAchievement->setGameAchievement($gameAchievement);

            $this->em->persist($userAchievement);

            if ($flush) {
                $this->em->flush();
            }
        }

        return $userAchievement;
    }

    /**
     * @param User|int $user
     *
     * @return float
     */
    public function getRating($user)
    {
        if (!$user instanceof User) {
            $user = $this->getUser($user);
        }

        $result = $this->em
            ->createQuery('
                SELECT SUM(100 - ga.percentage)
                FROM AppBundle:UserAchievement ua
                    JOIN ua.gameAchievement ga
                WHERE ua.user = :user
            ')
            ->setParameter('user', $user)
            ->getSingleResult()
        ;

        return current($result);
    }

    /**
     * Fetch and set actual user data
     *
     * @param User $user
     */
    public function updateUser(User $user)
    {
        $data = $this->steamApi->getUserData($user->getSteamid());

        $user
            ->setPersonaname($data['personaname'])
            ->setAvatar($data['avatar'])
            ->setIsBeingHandled(true)
        ;

        $this->em->persist($user);
        $this->em->flush();
        
        // TO DO: Run update command
    }
}