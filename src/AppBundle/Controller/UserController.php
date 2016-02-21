<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Method("GET")
     */
    public function loginAction(Request $request)
    {
    	if ($this->get('security.authorization_checker')->isGranted('ROLE_STEAM_USER')) {
    		return $this->redirectToRoute('user');
    	}

        $openid = new \LightOpenID($request->getHost());

        $openid->identity  = $this->container->getParameter('steam_identity');
        $openid->returnUrl = $this->generateUrl('auth', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->redirect($openid->authUrl());
    }

    /**
     * @Route("/auth", name="auth")
     * @Method("GET")
     */
    public function authAction(Request $request)
    {
        $openid = new \LightOpenID($request->getHost());

        if ($openid->mode == 'cancel' || !$openid->validate()) {
            return $this->redirectToRoute('homepage');
        }

        preg_match('/^.*\/(\d+)$/', $openid->identity, $matches);
        if (!isset($matches[1])) {
            return $this->redirectToRoute('homepage');
        }

        // TO DO: move following lines to the service
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->find($matches[1]);
        
        if (null === $user) {
            $user = new User();
            $user->setSteamid($id);
            $user->setPersonaname(''); // TO DO
            $user->setAvatar(''); // TO DO

            $em->persist($user);
            $em->flush();
        }

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);

        $event = new InteractiveLoginEvent($request, $token);
        $this->get('event_dispatcher')->dispatch('security.interactive_login', $event);
        
        return $this->redirectToRoute('user');
    }

    /**
     * @Route("/logout", name="logout")
     * @Method("GET")
     */
    public function logoutAction(Request $request)
    {
        $this->get('security.token_storage')->setToken(null);
        $request->getSession()->invalidate();

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/user", name="user")
     * @Method("GET")
     */
    public function userAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_STEAM_USER')) {
            return $this->redirectToRoute('homepage');
        }
        
        dump($this->getUser()->getSteamid());
        dump($this->get('steam_api')->getGameAchievements(
            //$this->getUser()->getSteamid(),
            39500
            //440
        ));exit;
    }
}
