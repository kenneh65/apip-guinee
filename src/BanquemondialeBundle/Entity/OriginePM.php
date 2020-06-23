<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OriginePM
 *
 * @ORM\Table(name="originepm")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\OriginePMRepository")
 */
class OriginePM
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
     * @ORM\Column(name="NomExploitant", type="string", length=255, nullable=true)
     */
    private $nomExploitant;

    /**
     * @var string
     *
     * @ORM\Column(name="PrenomExploitant", type="string", length=80, nullable=true)
     */
    private $prenomExploitant;

    /**
     * @var string
     *
     * @ORM\Column(name="AdresseExploitant", type="string", length=255, nullable=true)
     */
    private $adresseExploitant;

    /**
     * @var string
     *
     * @ORM\Column(name="RccmExploitant", type="string", length=30, nullable=true)
     */
    private $rccmExploitant;

    /**
     * @var string
     *
     * @ORM\Column(name="LoueurFondExploitant", type="string", length=255, nullable=true)
     */
    private $loueurFondExploitant;

    /**
     * @var bool
     *
     * @ORM\Column(name="SiExploitant", type="boolean", nullable=false)
     */
    private $siExploitant;

    /**
     * @var bool
     *
     * @ORM\Column(name="SiEtablissementSecondaire", type="boolean", nullable=false)
     */
    private $siEtablissementSecondaire;

    /**
     * @var string
     *
     * @ORM\Column(name="AdresseEtablissementSecondaire", type="string", length=255, nullable=true)
     */
    private $adresseEtablissementSecondaire;
	
	/**
     * @var string
     *
     * @ORM\Column(name="NomCommercial", type="string", length=255, nullable=true)
     */
    private $nomCommercial;
	
	/**
     * @var string
     *
     * @ORM\Column(name="SigleOuEnseigne", type="string", length=255, nullable=true)
     */
    private $sigleOuEnseigne;
	
	/**
     * @var date
     *
     * @ORM\Column(name="DateOuverture", type="date", nullable=true)
     */
    private $dateOuverture;

    /**
     * @var string
     *
     * @ORM\Column(name="ActiviteEtablissementSecondaire", type="string", length=255, nullable=true)
     */
    private $activiteEtablissementSecondaire;
	
	/**
     * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\TypeOrigine")
     * @ORM\JoinColumn(name="idTypeOrigine", referencedColumnName="id")    
     */
    private $typeOrigine;
	
	/**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\DossierDemande")
     * @ORM\JoinColumn(name="idDossierDemande", referencedColumnName="id")    
     */
    private $dossierDemande;


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
     * Set nomExploitant
     *
     * @param string $nomExploitant
     * @return OriginePM
     */
    public function setNomExploitant($nomExploitant)
    {
        $this->nomExploitant = $nomExploitant;

        return $this;
    }

    /**
     * Get nomExploitant
     *
     * @return string 
     */
    public function getNomExploitant()
    {
        return $this->nomExploitant;
    }

    /**
     * Set prenomExploitant
     *
     * @param string $prenomExploitant
     * @return OriginePM
     */
    public function setPrenomExploitant($prenomExploitant)
    {
        $this->prenomExploitant = $prenomExploitant;

        return $this;
    }

    /**
     * Get prenomExploitant
     *
     * @return string 
     */
    public function getPrenomExploitant()
    {
        return $this->prenomExploitant;
    }

    /**
     * Set adresseExploitant
     *
     * @param string $adresseExploitant
     * @return OriginePM
     */
    public function setAdresseExploitant($adresseExploitant)
    {
        $this->adresseExploitant = $adresseExploitant;

        return $this;
    }

    /**
     * Get adresseExploitant
     *
     * @return string 
     */
    public function getAdresseExploitant()
    {
        return $this->adresseExploitant;
    }
	
	/**
     * Set nomCommercial
     *
     * @param string $nomCommercial
     * @return OriginePM
     */
    public function setNomCommercial($nomCommercial)
    {
        $this->nomCommercial = $nomCommercial;

        return $this;
    }

    /**
     * Get nomCommercial
     *
     * @return string 
     */
    public function getNomCommercial()
    {
        return $this->nomCommercial;
    }
	
	/**
     * Set sigleOuEnseigne
     *
     * @param string $sigleOuEnseigne
     * @return OriginePM
     */
    public function setSigleOuEnseigne($sigleOuEnseigne)
    {
        $this->sigleOuEnseigne = $sigleOuEnseigne;

        return $this;
    }

    /**
     * Get sigleOuEnseigne
     *
     * @return string 
     */
    public function getSigleOuEnseigne()
    {
        return $this->sigleOuEnseigne;
    }
	
	/**
     * Set dateOuverture
     *
     * @param date $dateOuverture
     * @return OriginePM
     */
    public function setDateOuverture($dateOuverture)
    {
        $this->dateOuverture = $dateOuverture;

        return $this;
    }

    /**
     * Get dateOuverture
     *
     * @return date 
     */
    public function getDateOuverture()
    {
        return $this->dateOuverture;
    }

    /**
     * Set rccmExploitant
     *
     * @param string $rccmExploitant
     * @return OriginePM
     */
    public function setRccmExploitant($rccmExploitant)
    {
        $this->rccmExploitant = $rccmExploitant;

        return $this;
    }

    /**
     * Get rccmExploitant
     *
     * @return string 
     */
    public function getRccmExploitant()
    {
        return $this->rccmExploitant;
    }

    /**
     * Set loueurFondExploitant
     *
     * @param string $loueurFondExploitant
     * @return OriginePM
     */
    public function setLoueurFondExploitant($loueurFondExploitant)
    {
        $this->loueurFondExploitant = $loueurFondExploitant;

        return $this;
    }

    /**
     * Get loueurFondExploitant
     *
     * @return string 
     */
    public function getLoueurFondExploitant()
    {
        return $this->loueurFondExploitant;
    }

    /**
     * Set siExploitant
     *
     * @param boolean $siExploitant
     * @return OriginePM
     */
    public function setSiExploitant($siExploitant)
    {
        $this->siExploitant = $siExploitant;

        return $this;
    }

    /**
     * Get siExploitant
     *
     * @return boolean 
     */
    public function getSiExploitant()
    {
        return $this->siExploitant;
    }

    /**
     * Set siEtablissementSecondaire
     *
     * @param boolean $siEtablissementSecondaire
     * @return OriginePM
     */
    public function setSiEtablissementSecondaire($siEtablissementSecondaire)
    {
        $this->siEtablissementSecondaire = $siEtablissementSecondaire;

        return $this;
    }

    /**
     * Get siEtablissementSecondaire
     *
     * @return boolean 
     */
    public function getSiEtablissementSecondaire()
    {
        return $this->siEtablissementSecondaire;
    }

    /**
     * Set adresseEtablissementSecondaire
     *
     * @param string $adresseEtablissementSecondaire
     * @return OriginePM
     */
    public function setAdresseEtablissementSecondaire($adresseEtablissementSecondaire)
    {
        $this->adresseEtablissementSecondaire = $adresseEtablissementSecondaire;

        return $this;
    }

    /**
     * Get adresseEtablissementSecondaire
     *
     * @return string 
     */
    public function getAdresseEtablissementSecondaire()
    {
        return $this->adresseEtablissementSecondaire;
    }

    /**
     * Set activiteEtablissementSecondaire
     *
     * @param string $activiteEtablissementSecondaire
     * @return OriginePM
     */
    public function setActiviteEtablissementSecondaire($activiteEtablissementSecondaire)
    {
        $this->activiteEtablissementSecondaire = $activiteEtablissementSecondaire;

        return $this;
    }

    /**
     * Get activiteEtablissementSecondaire
     *
     * @return string 
     */
    public function getActiviteEtablissementSecondaire()
    {
        return $this->activiteEtablissementSecondaire;
    }
	
	 function getTypeOrigine() {
        return $this->typeOrigine;
    }

    function setTypeOrigine(\ParametrageBundle\Entity\TypeOrigine $typeOrigine = null) {
        $this->typeOrigine = $typeOrigine;
    }
	
	function getDossierDemande() {
        return $this->dossierDemande;
    }

    function setDossierDemande(\BanquemondialeBundle\Entity\DossierDemande $dossierDemande = null) {
        $this->dossierDemande = $dossierDemande;
    }
}
