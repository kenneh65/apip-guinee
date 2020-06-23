<?php

namespace BanquemondialeBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * CommissionnaireAuCompte
 *
 * @ORM\Table(name="commissionnaireAuCompte")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\CommissionnaireAuCompteRepository")
 */
class CommissionnaireAuCompte {

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
     * @ORM\Column(name="prenom", type="string", length=255,nullable=true)
     */
    private $prenom;

    /**
     * @var \DateTime
    * @Assert\Date()
     * @Assert\LessThan("-18 years")
      * @ORM\Column(name="dateNaissance", type="date",nullable=true)
     */
    private $dateNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="lieuNaissance", type="string", length=150,nullable=true)
     */
    private $lieuNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=250,nullable=true)
     */
    private $adresse;
	
	/**
     * @var string
     *
     * @ORM\Column(name="telFax", type="string", length=100,nullable=true)
     */
    private $telFax;
	
	/**
     * @var string
     *
     * @ORM\Column(name="bp", type="string", length=100,nullable=true)
     */
    private $bp;
	
	/**
     * @var string
     * @ORM\Column(name="email", type="string", length=100,nullable=true)
     */
    private $email;
	
	
	
	/**
     * @var string
     *
     * @ORM\Column(name="numeroAffiliation", type="string", length=100)
     */
    private $numeroAffiliation;
	
	/**
     * @var string
     *
     * @ORM\Column(name="types", type="string", length=100,nullable=true)
     */
    private $types;
	
	/**
     * @var boolean
     * @ORM\Column(name="actif",type="boolean",nullable=true)
     */
    private $actif;
    
/**
     * @var string
     *     
     */
    private $fullName;
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return CommissionnaireAuCompte
     */
    public function setNom($nom) {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom() {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return CommissionnaireAuCompte
     */
    public function setPrenom($prenom) {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom() {
        return $this->prenom;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     * @return CommissionnaireAuCompte
     */
    public function setDateNaissance($dateNaissance) {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime 
     */
    public function getDateNaissance() {
        return $this->dateNaissance;
    }

    /**
     * Set lieuNaissance
     *
     * @param string $lieuNaissance
     * @return CommissionnaireAuCompte
     */
    public function setLieuNaissance($lieuNaissance) {
        $this->lieuNaissance = $lieuNaissance;

        return $this;
    }

    /**
     * Get lieuNaissance
     *
     * @return string 
     */
    public function getLieuNaissance() {
        return $this->lieuNaissance;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return CommissionnaireAuCompte
     */
    public function setAdresse($adresse) {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string 
     */
    public function getAdresse() {
        return $this->adresse;
    }
	
	/**
     * Set adresse
     *
     * @param string $telephone
     * @return CommissionnaireAuCompte
     */
    public function setTelephone($telephone) {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone() {
        return $this->telephone;
    }
	
	/**
     * Set bpfax
     *
     * @param string $bpfax
     * @return CommissionnaireAuCompte
     */
    public function setBpfax($bpfax) {
        $this->bpfax = $bpfax;

        return $this;
    }

    /**
     * Get bpfax
     *
     * @return string 
     */
    public function getBpfax() {
        return $this->bpfax;
    }
	
	
	 /**
     * Set email
     *
     * @param string $email
     * @return CommissionnaireAuCompte
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
     * Set numeroAffiliation
     *
     * @param string $numeroAffiliation
     * @return CommissionnaireAuCompte
     */
    public function setNumeroAffiliation($numeroAffiliation) {
        $this->numeroAffiliation = $numeroAffiliation;

        return $this;
    }

    /**
     * Get numeroAffiliation
     *
     * @return string 
     */
    public function getNumeroAffiliation() {
        return $this->numeroAffiliation;
    }
	
	/**
     * Set types
     *
     * @param string $types
     * @return CommissionnaireAuCompte
     */
    public function setTypes($types) {
        $this->types = $types;

        return $this;
    }

    /**
     * Get types
     *
     * @return string 
     */
    public function getTypes() {
        return $this->types;
    }
	
	
	/**
     * Set actif
     *
     * @param boolean $actif
     * @return Langue
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean 
     */
    public function getActif()
    {
        return $this->actif;
    }
	
	
	
	
	
	

    public function __toString() {
        return $this->getPrenom() . ' ' . $this->getNom();
    }


    public function getFullName(){
        return $this->getPrenom() . ' ' . $this->getNom();
    }


    /**
     * Set telFax
     *
     * @param string $telFax
     * @return CommissionnaireAuCompte
     */
    public function setTelFax($telFax)
    {
        $this->telFax = $telFax;

        return $this;
    }

    /**
     * Get telFax
     *
     * @return string 
     */
    public function getTelFax()
    {
        return $this->telFax;
    }

    /**
     * Set bp
     *
     * @param string $bp
     * @return CommissionnaireAuCompte
     */
    public function setBp($bp)
    {
        $this->bp = $bp;

        return $this;
    }

    /**
     * Get bp
     *
     * @return string 
     */
    public function getBp()
    {
        return $this->bp;
    }
}
