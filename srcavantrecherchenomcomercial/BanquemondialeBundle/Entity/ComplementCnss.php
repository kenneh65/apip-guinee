<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * cnss
 *
 * @ORM\Table(name="complementCnss")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\ComplementCnssRepository")
 */
class ComplementCnss
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
     * @ORM\Column(name="numeroemployeur", type="string", length=80, unique=true)
     */
    private $numeroEmployeur;
	
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateImmatriculation", type="date")
     */
    private $dateImmatriculation;
	
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEffet", type="date")
     */
    private $dateEffet;

    /**
     * @var string
     *
     * @ORM\Column(name="categorie", type="string", length=80)
     */
    private $categorie;

    /**
     * @var int
     *
     * @ORM\Column(name="plafonne", type="integer")
     */
    private $plafonne;

    /**
     * @var int
     *
     * @ORM\Column(name="plancher", type="integer")
     */
    private $plancher;
    
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
     * Set numeroEmployeur
     *
     * @param string $numeroEmployeur
     * @return complementCnss
     */
    public function setNumeroEmployeur($numeroEmployeur)
    {
        $this->numeroEmployeur = $numeroEmployeur;

        return $this;
    }

    /**
     * Get numeroEmployeur
     *
     * @return string 
     */
    public function getNumeroEmployeur()
    {
        return $this->numeroEmployeur;
    }

    /**
     * Set dateImmatriculation
     *
     * @param \DateTime $dateImmatriculation
     * @return complementCnss
     */
    public function setDateImmatriculation($dateImmatriculation)
    {
        $this->dateImmatriculation = $dateImmatriculation;

        return $this;
    }

    /**
     * Get dateImmatriculation
     *
     * @return \DateTime 
     */
    public function getDateImmatriculation()
    {
        return $this->dateImmatriculation;
    }
	
	/**
     * Set dateEffet
     *
     * @param \DateTime $dateEffet
     * @return complementCnss
     */
    public function setDateEffet($dateEffet)
    {
        $this->dateEffet = $dateEffet;

        return $this;
    }

    /**
     * Get dateEffet
     *
     * @return \DateTime 
     */
    public function getDateEffet()
    {
        return $this->dateEffet;
    }

    /**
     * Set categorie
     *
     * @param string $categorie
     * @return complementCnss
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return string 
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set plafonne
     *
     * @param integer $plafonne
     * @return complementCnss
     */
    public function setPlafonne($plafonne)
    {
        $this->plafonne = $plafonne;

        return $this;
    }

    /**
     * Get plafonne
     *
     * @return integer 
     */
    public function getPlafonne()
    {
        return $this->plafonne;
    }

    /**
     * Set plancher
     *
     * @param integer $plancher
     * @return ComplementCnss
     */
    public function setPlancher($plancher)
    {
        $this->plancher = $plancher;

        return $this;
    }

    /**
     * Get plancher
     *
     * @return integer 
     */
    public function getPlancher()
    {
        return $this->plancher;
    }    
	
	function getDossierDemande() {
        return $this->dossierDemande;
    }

    function setDossierDemande(\BanquemondialeBundle\Entity\DossierDemande $dossierDemande = null) {
        $this->dossierDemande = $dossierDemande;
    }
}
