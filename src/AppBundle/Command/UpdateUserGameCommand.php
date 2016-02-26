<?php

namespace AppBundle\Command;

use AppBundle\Entity\UserGame;

class UpdateUserGameCommand extends BaseUpdateCommand
{
    /**
     * @var UserGame
     */
    protected $userGame;

    protected function configure()
    {
        $this
            ->setName('steameter:update:game')
            ->setDescription('Run the game update')
        ;

        parent::configure();
    }

    protected function update()
    {
        $this->userGame = $this->getNextUserGame();

        if (!$this->userGame instanceof UserGame) {
            return $this->updateUser();
        }

        $this
            //->startUpdate()
            ->updateGameAchievements()
            ->updateUserAchievements()
            //->finishUpdate()
        ;

        // TO DO: Run next update game command
    }

    /**
     * @return UserGame|null
     */
    protected function getNextUserGame()
    {
        return $this->em
            ->createQuery('
                SELECT ug
                FROM AppBundle:UserGame ug
                WHERE ug.user = :user
                    AND ug.isBeingHandled = :handled
                    AND (ug.updatedAt IS NULL OR ug.updatedAt < :updated)

            ')
            ->setParameters([
                'user'    => $this->user,
                'handled' => false,
                'updated' => new \DateTime('-1 day')
            ])
            ->setMaxResults(1)
            ->getSingleResult()
        ;
    }

    protected function updateUser()
    {
        $this->user->setIsBeingHandled(false);
        $this->user->setUpdatedAt(new \DateTime());
        // TO DO: Set rating

        $this->em->persist($this->user);
        $this->em->flush();
    }

    /**
     * @return UpdateUserGameCommand
     */
    protected function startUpdate()
    {
        $this->userGame->setIsBeingHandled(true);

        $this->em->persist($this->userGame);
        $this->em->flush();

        return $this;
    }

    /**
     * @return UpdateUserGameCommand
     */
    protected function updateGameAchievements()
    {
        $game = $this->userGame->getGame();

        if (!$game->isOutdated()) {
            return $this;
        }

        $achievementsList = $this->steamApi->getGameAchievements($game->getGameid());

        foreach ($achievementsList as $achievementData) {
            $gameAchievement = $this->steamData->getGameAchievement($game, $achievementData['name'], false);
            $gameAchievement->setPercentage($achievementData['percent']);
            $gameAchievement->setCheckedAt($this->checkedAt);

            $this->em->persist($gameAchievement);
        }

        $this->em->flush();

        $this->em
            ->createQuery('
                DELETE AppBundle:GameAchievement ga
                WHERE ga.checkedAt != :checkedAt
            ')
            ->setParameter('checkedAt', $this->checkedAt)
            ->execute()
        ;

        return $this;
    }

    /**
     * @return UpdateUserGameCommand
     */
    protected function updateUserAchievements()
    {
        return $this;
    }

    /**
     * @return UpdateUserGameCommand
     */
    protected function finishUpdate()
    {
        $this->userGame->setIsBeingHandled(false);
        $this->userGame->setUpdatedAt(new \DateTime());

        $this->em->persist($this->userGame);
        $this->em->flush();

        return $this;
    }
}