<?php

namespace UtilisateursBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="UtilisateursBundle\Repository\MessageRepository")
 */
class Message
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var string
     * @ORM\Column(name="objet",type="string", length=100)
     */
    private $objet;
    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_read", type="boolean")
     */
    private $isRead;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_envoie", type="datetime")
     */
    private $dateEnvoie;

    /**
     * @ORM\ManyToOne(targetEntity="Utilisateurs",inversedBy="messages")
     * 
     */
    private $destinataire;
    
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set isRead
     *
     * @param boolean $isRead
     * @return Message
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * Get isRead
     *
     * @return boolean 
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * Set dateEnvoie
     *
     * @param \DateTime $dateEnvoie
     * @return Message
     */
    public function setDateEnvoie($dateEnvoie)
    {
        $this->dateEnvoie = $dateEnvoie;

        return $this;
    }

    /**
     * Get dateEnvoie
     *
     * @return \DateTime 
     */
    public function getDateEnvoie()
    {
        return $this->dateEnvoie;
    }
    
    public function __construct() {
        $this->dateEnvoie = new \Datetime();
        $this->isRead = false;
    }

    /**
     * Set destinataire
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $destinataire
     * @return Message
     */
    public function setDestinataire(\UtilisateursBundle\Entity\Utilisateurs $destinataire = null)
    {
        $this->destinataire = $destinataire;

        return $this;
    }

    /**
     * Get destinataire
     *
     * @return \UtilisateursBundle\Entity\Utilisateurs 
     */
    public function getDestinataire()
    {
        return $this->destinataire;
    }

    /**
     * Set objet
     *
     * @param string $objet
     * @return Message
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get objet
     *
     * @return string 
     */
    public function getObjet()
    {
        return $this->objet;
    }
}
