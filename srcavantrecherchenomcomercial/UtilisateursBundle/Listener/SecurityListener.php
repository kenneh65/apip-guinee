<?php

namespace UtilisateursBundle\Listener;

use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;

class SecurityListener {

    private $em;
    private $securityContext;
    private $container;
    protected $requestStack;

    public function __construct(SecurityContextInterface $context, Doctrine $doctrine, Container $container, RequestStack $requestStack) {
        $this->securityContext = $context;
        $this->container = $container;
        $this->em = $doctrine->getManager();
        $this->requestStack = $requestStack;
    }

    protected function getRequest() {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * onAuthenticationFailure
     *
     * @author 	Joe Sexton <joe@webtipblog.com>
     * @param 	AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event) {

        // executes on failed login

        $request = $this->getRequest();
        $cookies = $request->cookies;
        $response = new Response();

        if ($cookies->has('tentative')) {
            $cookie = $cookies->get('tentative');
            if ($cookie >= 3) {
//                $user = $this->em->getRepository('UtilisateursBundle:Utilisateurs')->findOneByUsername($request->get('_username'));
//                if ($user) {
//                    $user->setEnabled(false);
//                    $this->em->persist($user);
//
//                    $this->em->flush();
//                    $response->headers->clearCookie('tentative');
//                }
                $translated = $translated = $this->container->get('translator')->trans("nombre_tentative_atteint");
                $this->container->get('session')->getFlashBag()->set(
                        'info', $translated);
            } else {
                $cookie++;

                $response->headers->setCookie(new Cookie('tentative', $cookie, time() + 3600 * 3));
            }
        } else {
            $cookie_info = array(
                'name' => 'tentative', // Nom du cookie
                'value' => 1, // Valeur du cookie
                'time' => time() + 3600 * 24 * 7 // Durée de vie du cookie
            );
            $response->headers->setCookie(new Cookie($cookie_info['name'], $cookie_info['value'], $cookie_info['time']));
        }
        $response->send();
    }

    /**
     * onAuthenticationSuccess
     *
     * @author 	Joe Sexton <joe@webtipblog.com>
     * @param 	InteractiveLoginEvent $event
     */
    public function onAuthenticationSuccess(InteractiveLoginEvent $event) {
        // executes on successful login
        // First get that user object so we can work with it
        $request = $this->getRequest();
        $cookies = $request->cookies->all();
        $user = $event->getAuthenticationToken()->getUser();
        // Now check to see if they're a subscriber
        // Check their expiry date versus now
      
            $response = new Response();
            $response->headers->clearCookie('tentative');
            $response->send();
       

        // We now have to log out all other users that are sharing the same username outside of the current session token
        // ... This is code where I would detach all other `imcqBundle:Session` entities with a userId = currently logged in user
    }

}
