<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Pages
 *
 * @ORM\Table(name="news")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\NewsRepository")
 */
class News
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
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;

      /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_publication", type="datetime")
     */
    private $date_publication;

        /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_expiration", type="datetime")
     */
    private $date_expiration;

/**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime")
     */
    private $dateModification;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_ip", type="string", length=11)
     */
    private $adresseIp;
 /**
     * @ORM\OneToMany(targetEntity="NewsTraduction",mappedBy="news",cascade={"remove","persist"})
     */
    private  $traduction;
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
     * Set titre
     *
     * @param string $titre
     * @return Pages
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return Pages
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set date_publication
     *
     * @param \DateTime $datePublication
     * @return News
     */
    public function setDatePublication($datePublication)
    {
        $this->date_publication = $datePublication;

        return $this;
    }

    /**
     * Get date_publication
     *
     * @return \DateTime 
     */
    public function getDatePublication()
    {
        return $this->date_publication;
    }

    /**
     * Set date_expiration
     *
     * @param \DateTime $dateExpiration
     * @return News
     */
    public function setDateExpiration($dateExpiration)
    {
        $this->date_expiration = $dateExpiration;

        return $this;
    }

    /**
     * Get date_expiration
     *
     * @return \DateTime 
     */
    public function getDateExpiration()
    {
        return $this->date_expiration;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     * @return News
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime 
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set adresseIp
     *
     * @param string $adresseIp
     * @return News
     */
    public function setAdresseIp($adresseIp)
    {
        $this->adresseIp = $adresseIp;

        return $this;
    }

    /**
     * Get adresseIp
     *
     * @return string 
     */
    public function getAdresseIp()
    {
        return $this->adresseIp;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
       $this->dateExpiration = new \DateTime();
     $this->datePublication= new \DateTime();
         
    }

    /**
     * Add traduction
     *
     * @param \ParametrageBundle\Entity\NewsTraduction $traduction
     * @return News
     */
    public function addTraduction(\ParametrageBundle\Entity\NewsTraduction $traduction)
    {
        $this->traduction[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \ParametrageBundle\Entity\NewsTraduction $traduction
     */
    public function removeTraduction(\ParametrageBundle\Entity\NewsTraduction $traduction)
    {
        $this->traduction->removeElement($traduction);
    }

    /**
     * Get traduction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTraduction()
    {
        return $this->traduction;
    }
}
