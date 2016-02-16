<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomepageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
    	if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
    		return $this->redirectToRoute('user');
    	}

        return $this->render('AppBundle:Homepage:index.html.twig');
    }
}
