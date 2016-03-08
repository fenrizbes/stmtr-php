<?php

namespace AppBundle\Command;

class UpdateUserCommand extends BaseUpdateCommand
{
    protected function configure()
    {
        $this
            ->setName('steameter:update:user')
            ->setDescription('Run the full user update')
        ;

        parent::configure();
    }

    /**
     * @throws RuntimeException
     */
    protected function update()
    {
        $this
            ->persistGames()
            ->deleteRemoved()
        ;

        $this->steamData->runUpdateCommand('games', [
            $this->user->getSteamid()
        ]);
    }

    /**
     * @return UpdateUserCommand
     */
    protected function persistGames()
    {
        $gameList = $this->steamApi->getUserGames($this->user->getSteamid());

        $this->user->setGamesOwned(count($gameList));
        $this->em->persist($this->user);

        foreach ($gameList as $gameData) {
            if (
                !$gameData['playtime_forever']
                ||
                !array_key_exists('has_community_visible_stats', $gameData)
                ||
                !$gameData['has_community_visible_stats']
            ) {
                continue;
            }

            $userGame = $this->steamData->getUserGame($this->user, $gameData['appid'], false);
            $userGame->setCurrentPlaytime($gameData['playtime_forever']);
            $userGame->setCheckedAt($this->checkedAt);

            $this->em->persist($userGame);
        }

        $this->em->flush();

        return $this;
    }

    /**
     * @return UpdateUserCommand
     */
    protected function deleteRemoved()
    {
        $this->em
            ->createQuery('
                DELETE AppBundle\Entity\UserGame ug
                WHERE ug.user = :user
                    AND ug.checkedAt != :checkedAt
            ')
            ->setParameters([
                'user'      => $this->user,
                'checkedAt' => $this->checkedAt
            ])
            ->execute()
        ;

        return $this;
    }
}