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
            ->updateGameAchievements()
            ->updateGame()
            ->updateUserAchievements()
            ->updateUserGame()
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
                    AND (ug.updatedAt IS NULL OR ug.updatedAt < :updated)
            ')
            ->setParameters([
                'user'    => $this->user,
                'updated' => new \DateTime('-1 day')
            ])
            ->setMaxResults(1)
            ->getSingleResult()
        ;
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
                WHERE ga.game = :game
                    AND ga.checkedAt != :checkedAt
            ')
            ->setParameters([
                'game'      => $game,
                'checkedAt' => $this->checkedAt
            ])
            ->execute()
        ;

        return $this;
    }

    /**
     * @return UpdateUserGameCommand
     */
    protected function updateGame()
    {
        $game = $this->userGame->getGame();
        $game->setUpdatedAt(new \DateTime());

        $this->em->persist($game);
        $this->em->flush();

        return $this;
    }

    /**
     * @return UpdateUserGameCommand
     */
    protected function updateUserAchievements()
    {
        $game = $this->userGame->getGame();

        if (!count($game->getAchievements())) {
            return $this;
        }

        $achievementsList = $this->steamApi->getUserAchievements(
            $this->user->getSteamid(),
            $game->getGameid()
        );

        foreach ($achievementsList as $achievementData) {
            if (!$achievementData['achieved']) {
                continue;
            }

            $userAchievement = $this->steamData->getUserAchievement(
                $this->user,
                $game,
                $achievementData['apiname'],
                false
            );

            $userAchievement->setCheckedAt($this->checkedAt);

            $this->em->persist($userAchievement);
        }

        $this->em->flush();

        $removedAchievements = $this->em
            ->createQuery('
                SELECT ua
                FROM AppBundle:UserAchievement ua
                    JOIN ua.gameAchievement ga
                WHERE ua.user = :user
                    AND ua.checkedAt != :checkedAt
                    AND ga.game = :game
            ')
            ->setParameters([
                'user'      => $this->user,
                'game'      => $game,
                'checkedAt' => $this->checkedAt
            ])
            ->getResult()
        ;

        foreach ($removedAchievements as $removedAchievement) {
            $this->em->remove($removedAchievement);
        }

        $this->em->flush();

        return $this;
    }

    /**
     * @return UpdateUserGameCommand
     */
    protected function updateUserGame()
    {
        $this->userGame->setUpdatedAt(new \DateTime());

        $this->em->persist($this->userGame);
        $this->em->flush();

        return $this;
    }

    protected function updateUser()
    {
        $this->user->setIsBeingHandled(false);
        $this->user->setUpdatedAt(new \DateTime());
        $this->user->setRating(
            $this->steamData->getRating($this->user)
        );

        $this->em->persist($this->user);
        $this->em->flush();
    }
}