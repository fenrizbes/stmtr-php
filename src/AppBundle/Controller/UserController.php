<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    /**
     * @Route("/auth", name="auth")
     * @Method("GET")
     */
    public function authAction()
    {
    	if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
    		return $this->redirectToRoute('user');
    	}

        return;
    }
}
