<?php

namespace AppBundle\Command;

use AppBundle\Entity\UserGame;

class UpdateUserGamesCommand extends BaseUpdateCommand
{
    const COMMANDS_LIMIT = 5;

    /**
     * @var array
     */
    protected $pids;

    protected function configure()
    {
        $this
            ->setName('steameter:update:games')
            ->setDescription('Run user games update')
        ;

        parent::configure();
    }

    protected function update()
    {
        $userGames = $this->getNextUserGames();

        if (!count($userGames)) {
            return $this->updateUser();
        }

        foreach ($userGames as $userGame) {
            $this->pids[] = $this->steamData->runUpdateCommand('game', [
                $this->user->getSteamid(),
                $userGame->getGame()->getGameid()
            ]);
        }

        do {
            usleep(500000);
        } while ($this->areCommandsRunning());

        $this->steamData->runUpdateCommand('games', [
            $this->user->getSteamid()
        ]);
    }

    /**
     * @return bool
     */
    protected function areCommandsRunning()
    {
        foreach ($this->pids as $key => $pid) {
            if (!$this->steamData->isCommandRunning($pid)) {
                unset($this->pids[$key]);
            }
        }

        return (bool) count($this->pids);
    }

    /**
     * @return UserGame[]
     */
    protected function getNextUserGames()
    {
        return $this->em
            ->createQuery('
                SELECT ug
                FROM AppBundle:UserGame ug
                WHERE ug.user = :user
                    AND (ug.updatedAt IS NULL OR ug.updatedAt < :updated)
            ')
            ->setParameters([
                'user'    => $this->user,
                'updated' => new \DateTime('-1 day')
            ])
            ->setMaxResults(static::COMMANDS_LIMIT)
            ->getResult()
        ;
    }

    protected function updateUser()
    {
        if (!$this->user->getIsBeingHandled()) {
            return;
        }

        $this->user->setIsBeingHandled(false);
        $this->user->setUpdatedAt(new \DateTime());
        $this->user->setRating(
            $this->steamData->getRating($this->user)
        );

        $this->em->persist($this->user);
        $this->em->flush();
    }
}