<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
     * Render new users' cards
     */
    public function newUsersAction($limit = 6)
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em
            ->createQuery('
                SELECT u
                FROM AppBundle\Entity\User u
                WHERE u.rating IS NOT NULL 
                ORDER BY u.createdAt DESC
            ')
            ->setMaxResults($limit)
            ->getResult()
        ;

        return $this->render('AppBundle:Homepage:new_users.html.twig', [
            'users' => $users
        ]);
    }
}
