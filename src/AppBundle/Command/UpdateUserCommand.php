<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use AppBundle\Entity\User;
use AppBundle\Entity\Game;
use AppBundle\Entity\UserGame;

class UpdateUserCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var AppBundle\Service\SteamAPIService
     */
    protected $steamApi;

    /**
     * @var AppBundle\Service\SteamDataService
     */
    protected $steamData;

    protected function configure()
    {
        $this
            ->setName('steameter:update:user')
            ->setDescription('Run the full user update')
            ->addArgument('steamid', InputArgument::REQUIRED, 'User\'s steamid')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $this->output    = $output;
        $this->em        = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->steamApi  = $this->getContainer()->get('steam_api');
        $this->steamData = $this->getContainer()->get('steam_data');

        $user = $this->em->getRepository('AppBundle:User')->find(
            (int) $input->getArgument('steamid')
        );

        if (!$user instanceof User) {
            throw new RuntimeException('User not found');
        }

        $this->updateUserGames($user);
    }

    protected function updateUserGames(User $user)
    {
        $checkedAt = new \DateTime();

        $this->output->writeln('<info>Receiving games...</info>');

        $gameList = $this->steamApi->getUserGames($user->getSteamid());

        $this->output->writeln('<info>Total amount: '. count($gameList) .'</info>');
        $this->output->writeln('<info>Persisting games...</info>');

        foreach ($gameList as $gameData) {
            $userGame = $this->steamData->getUserGame($user->getSteamid(), $gameData['appid'], false);
            $userGame->setCheckedAt($checkedAt);

            $this->em->persist($userGame);
        }

        $this->em->flush();

        $this->output->writeln('<info>Deleting removed games...</info>');

        $this->em
            ->createQuery('
                DELETE AppBundle\Entity\UserGame ug
                WHERE ug.checkedAt != :checkedAt
            ')
            ->setParameter('checkedAt', $checkedAt)
            ->execute()
        ;

        $this->output->writeln('<info>Control passed to UserGameUpdateCommand</info>');
    }
}