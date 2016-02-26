<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Exception\RuntimeException;

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
        if ($this->user->getIsBeingHandled()) {
            throw new RuntimeException('User is already being handled');
        }

        $this
            ->persistGames()
            ->deleteRemoved()
            ->updateUser()
        ;

        // TO DO: Run update game command
    }

    /**
     * @return UpdateUserCommand
     */
    protected function persistGames()
    {
        $gameList = $this->steamApi->getUserGames($this->user->getSteamid());

        foreach ($gameList as $gameData) {
            $userGame = $this->steamData->getUserGame($this->user, $gameData['appid'], false);
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
                WHERE ug.checkedAt != :checkedAt
            ')
            ->setParameter('checkedAt', $this->checkedAt)
            ->execute()
        ;

        return $this;
    }

    /**
     * @return UpdateUserCommand
     */
    protected function updateUser()
    {
        $this->user->setIsBeingHandled(true);

        $this->em->persist($this->user);
        $this->em->flush();

        return $this;
    }
}