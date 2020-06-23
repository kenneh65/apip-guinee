<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace UtilisateursBundle\Message;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use UtilisateursBundle\Entity\Message;

class Notification{
      private $doctrine;
    
    public function __construct(Doctrine $doctrine){
        $this->doctrine = $doctrine;
    }
    public function notifier($text,$destinataire,$objet){
        
        $message = new Message();
        $message->setMessage($text);
        $message->setObjet($objet);
        $em = $this->doctrine->getManager();
        $message->setDestinataire($destinataire);
        
        $em->persist($message);
        $em->flush();
        
    }
}