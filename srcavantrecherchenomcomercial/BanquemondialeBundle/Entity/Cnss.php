<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * cnss
 *
 * @ORM\Table(name="cnss")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\CnssRepository")
 */
class Cnss
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
     * @var bool
     *
     * @ORM\Column(name="personnel", type="boolean")
     */
    private $personnel;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePremierEmbauche", type="date")
     */
    private $datePremierEmbauche;

    /**
     * @var int
     *
     * @ORM\Column(name="effectifHomme", type="integer")
     */
    private $effectifHomme;

    /**
     * @var int
     *
     * @ORM\Column(name="effectifFemme", type="integer")
     */
    private $effectifFemme;

    /**
     * @var int
     *
     * @ORM\Column(name="effectifApprentis", type="integer")
     */
    private $effectifApprentis;

    /**
     * @var int
     *
     * @ORM\Column(name="effectifTotal", type="integer")
     */
    private $effectifTotal;

    /**
     * @var bool
     *
     * @ORM\Column(name="personnelDomestique", type="boolean")
     */
    private $personnelDomestique;
	
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
     * Set personnel
     *
     * @param boolean $personnel
     * @return cnss
     */
    public function setPersonnel($personnel)
    {
        $this->personnel = $personnel;

        return $this;
    }

    /**
     * Get personnel
     *
     * @return boolean 
     */
    public function getPersonnel()
    {
        return $this->personnel;
    }

    /**
     * Set datePremierEmbauche
     *
     * @param \DateTime $datePremierEmbauche
     * @return cnss
     */
    public function setDatePremierEmbauche($datePremierEmbauche)
    {
        $this->datePremierEmbauche = $datePremierEmbauche;

        return $this;
    }

    /**
     * Get datePremierEmbauche
     *
     * @return \DateTime 
     */
    public function getDatePremierEmbauche()
    {
        return $this->datePremierEmbauche;
    }

    /**
     * Set effectifHomme
     *
     * @param integer $effectifHomme
     * @return cnss
     */
    public function setEffectifHomme($effectifHomme)
    {
        $this->effectifHomme = $effectifHomme;

        return $this;
    }

    /**
     * Get effectifHomme
     *
     * @return integer 
     */
    public function getEffectifHomme()
    {
        return $this->effectifHomme;
    }

    /**
     * Set effectifFemme
     *
     * @param integer $effectifFemme
     * @return cnss
     */
    public function setEffectifFemme($effectifFemme)
    {
        $this->effectifFemme = $effectifFemme;

        return $this;
    }

    /**
     * Get effectifFemme
     *
     * @return integer 
     */
    public function getEffectifFemme()
    {
        return $this->effectifFemme;
    }

    /**
     * Set effectifApprentis
     *
     * @param integer $effectifApprentis
     * @return cnss
     */
    public function setEffectifApprentis($effectifApprentis)
    {
        $this->effectifApprentis = $effectifApprentis;

        return $this;
    }

    /**
     * Get effectifApprentis
     *
     * @return integer 
     */
    public function getEffectifApprentis()
    {
        return $this->effectifApprentis;
    }

    /**
     * Set effectifTotal
     *
     * @param integer $effectifTotal
     * @return cnss
     */
    public function setEffectifTotal($effectifTotal)
    {
        $this->effectifTotal = $effectifTotal;

        return $this;
    }

    /**
     * Get effectifTotal
     *
     * @return integer 
     */
    public function getEffectifTotal()
    {
        return $this->effectifTotal;
    }

    /**
     * Set personnelDomestique
     *
     * @param boolean $personnelDomestique
     * @return cnss
     */
    public function setPersonnelDomestique($personnelDomestique)
    {
        $this->personnelDomestique = $personnelDomestique;

        return $this;
    }

    /**
     * Get personnelDomestique
     *
     * @return boolean 
     */
    public function getPersonnelDomestique()
    {
        return $this->personnelDomestique;
    }
	
	function getDossierDemande() {
        return $this->dossierDemande;
    }

    function setDossierDemande(\BanquemondialeBundle\Entity\DossierDemande $dossierDemande = null) {
        $this->dossierDemande = $dossierDemande;
    }
}
