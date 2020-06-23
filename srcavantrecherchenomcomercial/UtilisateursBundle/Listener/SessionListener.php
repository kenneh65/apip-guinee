<?php

namespace UtilisateursBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Router;

/**
 * Custom session listener.
 */
class SessionListener {

    private $securityContext;
    private $container;
    private $router;

    public function __construct(SecurityContext $securityContext, Container $container, Router $router) {
        $this->securityContext = $securityContext;
        $this->container = $container;
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($token = $this->securityContext->getToken()) { // Check for a token - or else isGranted() will fail on the assets
            if ($this->securityContext->isGranted('ROLE_USER') || $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) { // Check if there is an authenticated user
                // Compare the stored session ID to the current session ID with the user
                if ($token->getUser() &&  $token->getUser()->getSessionId()!== $event->getRequest()->getSession()->getId()) {
                    // Tell the user that someone else has logged on with a different device
                   
                    $this->container->get('session')->getFlashBag()->set(
                            'error', '$translated'
                    );
                    // Kick this user out, because a new user has logged in
                    $this->securityContext->setToken(null);
                    // Redirect the user back to the login page, or else they'll still be trying to access the dashboard (which they no longer have access to)
                    $response = new RedirectResponse($this->router->generate('fos_user_security_login'));
                    $event->setResponse($response);
                    return $event;
                }
            }
        }
    }

}
