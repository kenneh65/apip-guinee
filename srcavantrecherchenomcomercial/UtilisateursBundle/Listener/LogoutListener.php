<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace UtilisateursBundle\Listener;

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use UtilisateursBundle\Entity\Utilisateurs;

/**
 * Description of LogoutListener
 *
 * @author fgueye
 */
class LogoutListener implements LogoutHandlerInterface {

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {

        $this->em = $em;
    }

    public function logout(Request $request, Response $response, TokenInterface $authToken) {                

        $userConnected = $authToken->getUser();
        $date = new \DateTime();
        $userConnected->setLastLogoutTime($date);
        $this->em->persist($userConnected);
        $this->em->flush();
        //die(dump($authToken->getUser()));

        return $response;
    }

}
