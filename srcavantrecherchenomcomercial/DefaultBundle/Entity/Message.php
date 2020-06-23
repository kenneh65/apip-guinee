<?php

namespace DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="messages")
 * @ORM\Entity(repositoryClass="DefaultBundle\Repository\MessageRepository")
 */
class Message {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;

    /**
     * @var bool
     *
     * @ORM\Column(name="isLocked", type="boolean")
     */
    private $isLocked;

    /**
     * @var bool
     *
     * @ORM\Column(name="isRead", type="boolean")
     */
    private $isRead;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEnvoie", type="datetime")
     */
    private $dateEnvoie;
    
    /**
     * @ORM\ManyToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;
	
	
    /**
     * @ORM\ManyToOne(targetEntity="Discussion",inversedBy="messages")
     */
    private $discussion;
	

    /**

     * @ORM\ManyToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs")

     * @ORM\JoinColumn(nullable=true)
    */
    private $deleteBy;
	

    /**
     * Constructor
     */
    public function __construct() {
        $this->dateEnvoie = new \DateTime();
        $this->setIsLocked(false);
        $this->setIsRead(false);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return Message
     */
    public function setContenu($contenu) {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu() {
        return $this->contenu;
    }

    /**
     * Set isLocked
     *
     * @param boolean $isLocked
     * @return Message
     */
    public function setIsLocked($isLocked) {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Get isLocked
     *
     * @return boolean 
     */
    public function getIsLocked() {
        return $this->isLocked;
    }

    /**
     * Set isRead
     *
     * @param string $isRead
     * @return Message
     */
    public function setIsRead($isRead) {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * Get isRead
     *
     * @return string 
     */
    public function getIsRead() {
        return $this->isRead;
    }

    /**
     * Set dateEnvoie
     *
     * @param \DateTime $dateEnvoie
     * @return Message
     */
    public function setDateEnvoie($dateEnvoie) {
        $this->dateEnvoie = $dateEnvoie;

        return $this;
    }

    /**
     * Get dateEnvoie
     *
     * @return \DateTime 
     */
    public function getDateEnvoie() {
        return $this->dateEnvoie;
    }


    /**
     * Set auteur
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $auteur
     * @return Message
     */
    public function setAuteur(\UtilisateursBundle\Entity\Utilisateurs $auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return \UtilisateursBundle\Entity\Utilisateurs 
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set discussion
     *
     * @param \DefaultBundle\Entity\Discussion $discussion
     * @return Message
     */
    public function setDiscussion(\DefaultBundle\Entity\Discussion $discussion = null)
    {
        $this->discussion = $discussion;

        return $this;
    }

    /**
     * Get discussion
     *
     * @return \DefaultBundle\Entity\Discussion 
     */
    public function getDiscussion()
    {
        return $this->discussion;
    }

    /**
     * Set deleteBy
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $deleteBy
     * @return Message
     */
    public function setDeleteBy(\UtilisateursBundle\Entity\Utilisateurs $deleteBy = null)
    {
        $this->deleteBy = $deleteBy;

        return $this;
    }

    /**
     * Get deleteBy
     *
     * @return \UtilisateursBundle\Entity\Utilisateurs 
     */
    public function getDeleteBy()
    {
        return $this->deleteBy;
    }
	
		

	
	
}
