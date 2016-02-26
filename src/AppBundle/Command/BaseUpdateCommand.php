<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use AppBundle\Entity\User;

abstract class BaseUpdateCommand extends ContainerAwareCommand
{
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

    /**
     * @var \DateTime
     */
    protected $checkedAt;

    /**
     * @var User
     */
    protected $user;

    protected function configure()
    {
        $this->addArgument('steamid', InputArgument::REQUIRED, 'User\'s steamid');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em        = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->steamApi  = $this->getContainer()->get('steam_api');
        $this->steamData = $this->getContainer()->get('steam_data');
        $this->checkedAt = new \DateTime();

        $this->user = $this->em->getRepository('AppBundle:User')->find(
            (int) $input->getArgument('steamid')
        );

        if (!$this->user instanceof User) {
            throw new RuntimeException('User not found');
        }

        $this->update();
    }

    abstract protected function update();
}