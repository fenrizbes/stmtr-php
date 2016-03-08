<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Game;
use AppBundle\Entity\UserGame;
use AppBundle\Entity\UserAchievement;
use AppBundle\Entity\GameAchievement;
use Doctrine\Common\Collections\Criteria;

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
     * @var string
     */
    protected $consolePath;

    /**
     * @param EntityManager $em
     * @param SteamAPIService $steamApi
     */
    public function __construct(EntityManager $em, SteamAPIService $steamApi, $consolePath)
    {
        $this->em          = $em;
        $this->steamApi    = $steamApi;
        $this->consolePath = $consolePath;
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
     * Return user's statistic information
     *
     * @param User $user
     *
     * @return array
     */
    public function getStatistics(User $user)
    {
        $hours = $this->em
            ->createQuery('
                SELECT SUM(ug.currentPlaytime / 60)
                FROM AppBundle:UserGame ug
                WHERE ug.user = :user
            ')
            ->setParameter('user', $user)
            ->getSingleResult()
        ;

        $achievements = $this->em
            ->createQuery('
                SELECT COUNT(ua)
                FROM AppBundle:UserAchievement ua
                WHERE ua.user = :user
            ')
            ->setParameter('user', $user)
            ->getSingleResult()
        ;

        return [
            'games'        => $user->getGamesOwned(),
            'hours'        => current($hours),
            'achievements' => current($achievements)
        ];
    }

    /**
     * Return information about a current updating proggress
     *
     * @param User|int $user
     *
     * @return array
     */
    public function getUserProgress($user)
    {
        if (!$user instanceof User) {
            $user = $this->getUser($user);
        }

        $data = [
            'in_progress' => $user->getIsBeingHandled(),
            'status'      => 'games',
            'percentage'  => 0
        ];

        if (!$data['in_progress']) {
            $data['status'] = 'finished';

            return $data;
        }

        $totalGames = $user->getGames()->count();

        if (!$totalGames) {
            return $data;
        }

        $criteria = Criteria::create()
            ->where(
                Criteria::expr()->gt('updatedAt', new \DateTime('-1 day'))
            )
        ;

        $updatedGames = $user
            ->getGames()
            ->matching($criteria)
            ->count()
        ;

        $data['percentage'] = $updatedGames * 100 / $totalGames;

        if ($data['percentage'] > 0) {
            $data['status'] = 'achievements';
        }

        return $data;
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
            ->setAvatar($data['avatar'])
            ->setIsBeingHandled(true)
        ;

        $this->em->persist($user);
        $this->em->flush();
        
        $this->runUpdateCommand('user', [
            $user->getSteamid()
        ]);
    }

    /**
     * Run a specific update command in background and return its PID
     *
     * @param User|int $steamid
     * @param string $name
     *
     * @return int
     */
    public function runUpdateCommand($name, array $arguments = [])
    {
        $pid = shell_exec(sprintf(
            'php %s steameter:update:%s %s > /dev/null 2>&1 & echo $!',
            $this->consolePath,
            $name,
            implode(' ', $arguments)
        ));

        return (int) trim($pid);
    }

    /**
     * Check if a command with specified PID is running
     *
     * @param int $pid
     *
     * @return bool
     */
    public function isCommandRunning($pid)
    {
        $output = shell_exec(sprintf('ps %d', $pid));

        if (count(split("\n", $output)) > 2) {
            return true;
        }

        return false;
    }
}