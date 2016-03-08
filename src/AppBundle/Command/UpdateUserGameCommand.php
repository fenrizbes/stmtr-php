<?php

namespace AppBundle\Command;

use AppBundle\Entity\UserGame;
use Symfony\Component\Console\Input\InputArgument;

class UpdateUserGameCommand extends BaseUpdateCommand
{
    /**
     * @var UserGame
     */
    protected $userGame;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('steameter:update:game')
            ->setDescription('Run the game update')
            ->addArgument('gameid', InputArgument::REQUIRED, 'Game\'s ID')
        ;
    }

    protected function update()
    {
        $this->userGame = $this->steamData->getUserGame(
            $this->user,
            (int) $this->input->getArgument('gameid')
        );

        $this
            ->updateGameAchievements()
            ->updateGame()
            ->updateUserAchievements()
            ->updateUserGame()
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

            $this->em->persist($gameAchievement);
        }

        $this->em->flush();

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
        if (!$this->userGame->isOutdated()) {
            return $this;
        }

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
        $this->userGame->setPreviousPlaytime(
            $this->userGame->getCurrentPlaytime()
        );

        $this->em->persist($this->userGame);
        $this->em->flush();

        return $this;
    }
}