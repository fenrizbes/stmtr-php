<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Query\ResultSetMapping;

class HomepageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     */
    public function indexAction()
    {
    	if ($this->get('security.authorization_checker')->isGranted('ROLE_STEAM_USER')) {
    		return $this->redirectToRoute('user');
    	}

        return $this->render('AppBundle:Homepage:index.html.twig');
    }

    /**
     * Render users' cards
     */
    public function usersAction($limit = 6)
    {
        $em = $this->getDoctrine()->getManager();

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('AppBundle\Entity\User', 'u');
        $rsm->addFieldResult('u', 'steamid', 'steamid');
        $rsm->addFieldResult('u', 'avatar', 'avatar');
        $rsm->addFieldResult('u', 'rating', 'rating');

        $users = $em
            ->createNativeQuery('
                SELECT *
                FROM user
                WHERE rating IS NOT NULL
                ORDER BY RAND()
                LIMIT :limit
            ', $rsm)
            ->setParameter('limit', $limit)
            ->getResult()
        ;

        return $this->render('AppBundle:Homepage:users.html.twig', [
            'users' => $users
        ]);
    }
}
