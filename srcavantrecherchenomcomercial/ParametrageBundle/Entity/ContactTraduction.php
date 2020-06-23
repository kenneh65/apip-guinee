<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactTraduction
 *
 * @ORM\Table(name="contact_traduction")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\ContactTraductionRepository")
 */
class ContactTraduction
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
     * @ORM\Column(name="nom", type="string", length=255)
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
     * @ORM\Column(name="email", type="string", length=255,nullable=true)
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
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Langue")
     */
    private $langue;
 /**
     * @ORM\ManyToOne(targetEntity="Contact",inversedBy="traduction")
     */
    private  $contact;
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
     * @return ContactTraduction
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
     * @return ContactTraduction
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
     * @return ContactTraduction
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
     * @return ContactTraduction
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
     * @return ContactTraduction
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
     * @return ContactTraduction
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
     * @return ContactTraduction
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
     * Set contact
     *
     * @param \ParametrageBundle\Entity\Contact $contact
     * @return ContactTraduction
     */
    public function setContact(\ParametrageBundle\Entity\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \ParametrageBundle\Entity\Contact 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set langue
     *
     * @param \BanquemondialeBundle\Entity\Langue $langue
     * @return ContactTraduction
     */
    public function setLangue(\BanquemondialeBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return \BanquemondialeBundle\Entity\Langue 
     */
    public function getLangue()
    {
        return $this->langue;
    }
}
