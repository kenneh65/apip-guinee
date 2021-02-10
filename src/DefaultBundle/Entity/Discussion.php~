<?php

namespace DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Discussion
 *
 * @ORM\Table(name="discussion")
 * @ORM\Entity(repositoryClass="DefaultBundle\Repository\DiscussionRepository")
 */
class Discussion {

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
     * @ORM\Column(name="objet", type="string", length=255)
     */
    private $objet;

    /**
     * @var bool
     *
     * @ORM\Column(name="isLocked", type="boolean")
     */
    private $isLocked;

    /**
     * @ORM\ManyToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs",inversedBy="discussionsInitiees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $expediteur;

    /**
     * @ORM\ManyToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs",inversedBy="discussions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $destinataire;

    /**
     * @ORM\OneToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs")
     * @ORM\JoinColumn(nullable=true)
     */
    private $lockedBy;

    /**
     * @ORM\OneToMany(targetEntity="Message",mappedBy="discussion",cascade={"remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $messages;
	

	/**
     * @var string
     *
     * @ORM\Column(name="numeroDossier", type="string", length=50, nullable=true)
     */
    private $numeroDossier;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set objet
     *
     * @param string $objet
     * @return Discussion
     */
    public function setObjet($objet) {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get objet
     *
     * @return string 
     */
    public function getObjet() {
        return $this->objet;
    }

    /**
     * Set isLocked
     *
     * @param boolean $isLocked
     * @return Discussion
     */
    public function setIsLocked($isLocked) {
        $this->isLocked = $isLocked;
  foreach($this->messages as $message)
            $message->setIsLocked($isLocked);
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
     * Constructor
     */
    public function __construct()
    {
        $this->isLocked=false;
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set expediteur
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $expediteur
     * @return Discussion
     */
    public function setExpediteur(\UtilisateursBundle\Entity\Utilisateurs $expediteur)
    {
        $this->expediteur = $expediteur;

        return $this;
    }

    /**
     * Get expediteur
     *
     * @return \UtilisateursBundle\Entity\Utilisateurs 
     */
    public function getExpediteur()
    {
        return $this->expediteur;
    }

    /**
     * Set destinataire
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $destinataire
     * @return Discussion
     */
    public function setDestinataire(\UtilisateursBundle\Entity\Utilisateurs $destinataire)
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
     * Set lockedBy
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $lockedBy
     * @return Discussion
     */
    public function setLockedBy(\UtilisateursBundle\Entity\Utilisateurs $lockedBy = null)
    {
        $this->lockedBy = $lockedBy;
        foreach($this->messages as $message)
            $message->setDeleteBy($lockedBy);
        return $this;
    }

    /**
     * Get lockedBy
     *
     * @return \UtilisateursBundle\Entity\Utilisateurs 
     */
    public function getLockedBy()
    {
        return $this->lockedBy;
    }

    /**
     * Add messages
     *
     * @param \DefaultBundle\Entity\Messages $messages
     * @return Discussion
     */
    public function addMessage(\DefaultBundle\Entity\Message $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \DefaultBundle\Entity\Messages $messages
     */
    public function removeMessage(\DefaultBundle\Entity\Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }
    public function getMessagesNonLus()
    {
        foreach ($this->messages as $message)
        {
            if($message->getIsRead()==false)
            {
                return $message;
            }
        }
        return null;
    }
	
	/**
     * Set numeroDossier
     *
     * @param string $numeroDossier
     * @return Discussion
     */
    public function setNumeroDossier($numeroDossier)
    {
        $this->numeroDossier = $numeroDossier;

        return $this;
    }

    /**
     * Get numeroDossier
     *
     * @return string 
     */
    public function getNumeroDossier()
    {
        return $this->numeroDossier;
    }
	
	
}
