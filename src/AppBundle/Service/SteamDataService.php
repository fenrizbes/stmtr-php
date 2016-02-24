<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;

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
     * @param EntityManager $em
     * @param SteamAPIService $steamApi
     */
    public function __construct(EntityManager $em, SteamAPIService $steamApi)
    {
        $this->em       = $em;
        $this->steamApi = $steamApi;
    }

    /**
     * Find or create a user by steamid
     *
     * @param string $steamid
     *
     * @return User
     */
    public function getUser($steamid)
    {
        $user = $this->em->getRepository('AppBundle:User')->find($steamid);
        
        if (null === $user) {
            $user = new User();
            $user->setSteamid($steamid);

            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
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
            ->setPersonaname($data['personaname'])
            ->setAvatar($data['avatar'])
            ->setIsBeingHandled(true)
        ;

        $this->em->persist($user);
        $this->em->flush();
        
        // TO DO: Run update command
    }
}