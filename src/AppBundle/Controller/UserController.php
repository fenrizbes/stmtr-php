<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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

        $user = $this->get('steam_data')->getUser($matches[1]);

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

        if ($this->getUser()->isOutdated()) {
            $this->get('steam_data')->updateUser($this->getUser());
        }

        return $this->render('AppBundle:User:index.html.twig', [
            'statistics' => $this->get('steam_data')->getStatistics($this->getUser())
        ]);
    }

    /**
     * @Route("/user/progress", name="user_progress")
     * @Method("GET")
     */
    public function progressAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_STEAM_USER')) {
            throw new AccessDeniedHttpException();
        }

        $data = $this->get('steam_data')->getUserProgress(
            $this->getUser()
        );

        $view = $this->renderView('AppBundle:User:progress.html.twig', array_merge([
            'statistics' => $this->get('steam_data')->getStatistics($this->getUser())
        ], $data));

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'view' => $view,
                'stop' => !$data['in_progress']
            ]);
        }

        return new Response($view);
    }

    /**
     * @Route("/user/{hash}.png", name="user_bar", requirements={
     *      "hash": "\w+"
     * })
     * @Method("GET")
     */
    public function barAction($hash)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneByHash($hash);

        if (!$user instanceof User) {
            throw $this->createNotFoundException();
        }

        $image = $this->get('userbar')->getImage($user);
        
        return new Response(file_get_contents($image), 200, [
            'Content-Type' => 'image/png'
        ]);
    }
}
