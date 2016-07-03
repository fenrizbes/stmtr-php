<?php

namespace AppBundle\Command;

use AppBundle\Entity\Game;

class UpdateRandomGameCommand extends UpdateUserGameCommand
{
    protected function configure()
    {
        $this
            ->setName('steameter:update:rndgame')
            ->setDescription('Run the random game update')
        ;
    }

    protected function loadUser()
    {
        return $this;
    }

    protected function update()
    {
        $this->game = $this->em
            ->createQuery('
                SELECT g
                FROM AppBundle:Game g
                WHERE g.updatedAt IS NULL
                    OR g.updatedAt < :updated
            ')
            ->setParameter('updated', new \DateTime('-1 day'))
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;

        if (!$this->game instanceof Game) {
            return;
        }

        $this
            ->updateGameAchievements()
            ->updateGame()
        ;
    }
}