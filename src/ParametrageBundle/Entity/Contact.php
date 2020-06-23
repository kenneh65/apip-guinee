<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contact
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\ContactRepository")
 */
class Contact
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
     * @ORM\Column(name="nom", type="string", length=250)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="fonction", type="string", length=50,nullable=true)
     */
    private $fonction;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=20,nullable=true)
     */
    private $telephone;
	
	/**
     * @var string
     *
     * @ORM\Column(name="telephone2", type="string", length=20,nullable=true)
     */
    private $telephone2;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;
	
	/**
     * @var string
     *
     * @ORM\Column(name="siteWeb", type="string", length=255,nullable=true)
     */
    private $siteWeb;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255,nullable=true)
     */
    private $adresse;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime")
     */
    private $dateModification;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_ip", type="string", length=12,nullable=true)
     */
    private $adresseIp;
    
    /**
     * @ORM\OneToMany(targetEntity="ContactTraduction",mappedBy="contact",cascade={"remove","persist"})
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
     * Set nom
     *
     * @param string $nom
     * @return Contact
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set fonction
     *
     * @param string $fonction
     * @return Contact
     */
    public function setFonction($fonction)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get fonction
     *
     * @return string 
     */
    public function getFonction()
    {
        return $this->fonction;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return Contact
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }
	
	
	/**
     * Set telephone2
     *
     * @param string $telephone2
     * @return Contact
     */
    public function setTelephone2($telephone2)
    {
        $this->telephone2 = $telephone2;

        return $this;
    }

    /**
     * Get telephone2
     *
     * @return string 
     */
    public function getTelephone2()
    {
        return $this->telephone2;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Contact
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set siteWeb
     *
     * @param string $siteWeb
     * @return Contact
     */
    public function setSiteWeb($siteWeb)
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    /**
     * Get siteWeb
     *
     * @return string 
     */
    public function getSiteWeb()
    {
        return $this->siteWeb;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return Contact
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string 
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     * @return Contact
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
     * @return Contact
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
        $this->traduction = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add traduction
     *
     * @param \ParametrageBundle\Entity\ContactTraduction $traduction
     * @return Contact
     */
    public function addTraduction(\ParametrageBundle\Entity\ContactTraduction $traduction)
    {
        $this->traduction[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \ParametrageBundle\Entity\ContactTraduction $traduction
     */
    public function removeTraduction(\ParametrageBundle\Entity\ContactTraduction $traduction)
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
