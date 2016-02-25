<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Game;
use AppBundle\Entity\UserGame;

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
     * @param int $steamid
     * @param int $gameid
     * @param bool $persist
     *
     * @return UserGame
     */
    public function getUserGame($steamid, $gameid, $flush = true)
    {
        $user = $this->getUser($steamid, $flush);
        $game = $this->getGame($gameid, $flush);

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