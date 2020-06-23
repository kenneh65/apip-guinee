<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pole
 *
 * @ORM\Table(name="pole")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\PoleRepository")
 */
class Pole {

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
     * @ORM\Column(name="nom", type="string", length=100, unique=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean", options={"default":true})
     */
    private $actif;

    /**
     * @ORM\OneToMany(targetEntity="UtilisateursBundle\Entity\Utilisateurs",mappedBy="pole",cascade={"remove"})
     */
    private $utilisateur;
	
	
	/**
     * @var bool
     *
     * @ORM\Column(name="isCreateur", type="boolean", options={"default"=true})
     */
    private $isCreateur;

	
	/**
      * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\TypePole")
      * @ORM\JoinColumn(name="idTypePole", referencedColumnName="id", nullable=true)
      */ 
    private $typePole;
	
		
	/**
     * @var string
     *
     * @ORM\Column(name="sigle", type="string", length=20, nullable=true)
     */
    private $sigle;
	

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
     * @return Pole
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
     * Set adresse
     *
     * @param string $adresse
     * @return Pole
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

    public function __toString() {
        return $this->getNom();
    }


    /**
     * Set utilisateur
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $utilisateur
     * @return Pole
     */
    public function setUtilisateur(\UtilisateursBundle\Entity\Utilisateurs $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \UtilisateursBundle\Entity\Utilisateurs 
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->utilisateur = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return Pole
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

    /**
     * Add utilisateur
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $utilisateur
     * @return Pole
     */
    public function addUtilisateur(\UtilisateursBundle\Entity\Utilisateurs $utilisateur)
    {
        $this->utilisateur[] = $utilisateur;

        return $this;
    }

    /**
     * Remove utilisateur
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $utilisateur
     */
    public function removeUtilisateur(\UtilisateursBundle\Entity\Utilisateurs $utilisateur)
    {
        $this->utilisateur->removeElement($utilisateur);
    }



    /**
     * Set gestionMonetaire
     *
     * @param boolean $gestionMonetaire
     * @return Pole
     */
    public function setGestionMonetaire($gestionMonetaire)
    {
        $this->gestionMonetaire = $gestionMonetaire;

        return $this;
    }

    /**
     * Get gestionMonetaire
     *
     * @return boolean 
     */
    public function getGestionMonetaire()
    {
        return $this->gestionMonetaire;
    }
  
    
    /**
     * Add typePole
     *
     * @param \ParametrageBundle\Entity\TypePole $typePole
     * @return Pole
     */
    public function addTypePole(\ParametrageBundle\Entity\TypePole $typePole)
    {
        $this->typePole[] = $typePole;

        return $this;
    }

    /**
     * Remove typePole
     *
     * @param \ParametrageBundle\Entity\TypePole $typePole
     */
    public function removeTypePole(\ParametrageBundle\Entity\TypePole $typePole)
    {
        $this->typePole->removeElement($typePole);
    }

    /**
     * Get typePole
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTypePole()
    {
        return $this->typePole;
    }

    /**
     * Set typePole
     *
     * @param \ParametrageBundle\Entity\TypePole $typePole
     * @return Pole
     */
    public function setTypePole(\ParametrageBundle\Entity\TypePole $typePole = null)
    {
        $this->typePole = $typePole;

        return $this;
    }
	
	/**
     * Set sigle
     *
     * @param string $sigle
     * @return Pole
     */
    public function setSigle($sigle) {
        $this->sigle = $sigle;

        return $this;
    }

    /**
     * Get sigle
     *
     * @return string 
     */
    public function getSigle() {
        return $this->sigle;
    }
	
	

    /**
     * Set isCreateur
     *
     * @param boolean $isCreateur
     * @return Pole
     */
    public function setIsCreateur($isCreateur)
    {
        $this->isCreateur = $isCreateur;

        return $this;
    }

    /**
     * Get isCreateur
     *
     * @return boolean 
     */
    public function getIsCreateur()
    {
        return $this->isCreateur;
    }
}
