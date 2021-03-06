<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Nif
 *
 * @ORM\Table(name="nif")
 * @UniqueEntity("numeroIdentificationFiscale",message="nif_existe")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\NifRepository")
 */
class Nif
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
     * @ORM\Column(name="numeroIdentificationFiscale", type="string", length=20, nullable=true)
     */
    private $numeroIdentificationFiscale;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroFormulaire", type="string", length=8, nullable=true)
     */
    private $numeroFormulaire;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroFormulaireBis", type="string", length=20, nullable=true)
     */
    private $numeroFormulaireBis;

    /**
     * @var string
     *
     * @ORM\Column(name="secteur", type="string", length=100, nullable=true)
     */
    private $secteur;

    /**
     * @var string
     *
     * @ORM\Column(name="quartier", type="string", length=100, nullable=true)
     */
    private $quartier;

    /**
     * @var string
     *
     * @ORM\Column(name="rue", type="string", length=100, nullable=true)
     */
    private $rue;

    /**
     * @var string
     *
     * @ORM\Column(name="marche", type="string", length=100, nullable=true)
     */
    private $marche;

    /**
     * @var string
     *
     * @ORM\Column(name="boutique", type="string", length=100, nullable=true)
     */
    private $boutique;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;
    /**
     * @var \DateTime
     * @ORM\Column(name="dateObtentionNif", type="datetime", nullable=true)
     */
    private $dateObtentionNif;
    /**
     * @var bool
     * @ORM\Column(name="statut", type="boolean", nullable=true)
     */
    private $statut;

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
     * Set numeroIdentificationFiscale
     *
     * @param string $numeroIdentificationFiscale
     * @return Nif
     */
    public function setNumeroIdentificationFiscale($numeroIdentificationFiscale)
    {
        $this->numeroIdentificationFiscale = $numeroIdentificationFiscale;

        return $this;
    }

    /**
     * Get numeroIdentificationFiscale
     *
     * @return string
     */
    public function getNumeroIdentificationFiscale()
    {
        return $this->numeroIdentificationFiscale;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Nif
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set dossierDemande
     *
     * @param \BanquemondialeBundle\Entity\DossierDemande $dossierDemande
     * @return Nif
     */
    public function setDossierDemande(\BanquemondialeBundle\Entity\DossierDemande $dossierDemande = null)
    {
        $this->dossierDemande = $dossierDemande;

        return $this;
    }

    /**
     * Get dossierDemande
     *
     * @return \BanquemondialeBundle\Entity\DossierDemande
     */
    public function getDossierDemande()
    {
        return $this->dossierDemande;
    }

    /**
     * Set secteur
     *
     * @param string $secteur
     * @return Nif
     */
    public function setSecteur($secteur)
    {
        $this->secteur = $secteur;

        return $this;
    }

    /**
     * Get secteur
     *
     * @return string
     */
    public function getSecteur()
    {
        return $this->secteur;
    }

    /**
     * Set marche
     *
     * @param string $marche
     * @return Nif
     */
    public function setMarche($marche)
    {
        $this->marche = $marche;

        return $this;
    }

    /**
     * Get marche
     *
     * @return string
     */
    public function getMarche()
    {
        return $this->marche;
    }

    /**
     * Set boutique
     *
     * @param string $boutique
     * @return Nif
     */
    public function setBoutique($boutique)
    {
        $this->boutique = $boutique;

        return $this;
    }

    /**
     * Get boutique
     *
     * @return string
     */
    public function getBoutique()
    {
        return $this->boutique;
    }


    /**
     * Set numeroFormulaire
     *
     * @param string $numeroFormulaire
     * @return Nif
     */
    public function setNumeroFormulaire($numeroFormulaire)
    {
        $this->numeroFormulaire = $numeroFormulaire;

        return $this;
    }

    /**
     * Get numeroFormulaire
     *
     * @return string
     */
    public function getNumeroFormulaire()
    {
        return $this->numeroFormulaire;
    }


    /**
     * Set numeroFormulaireBis
     *
     * @param string $numeroFormulaireBis
     * @return Nif
     */
    public function setNumeroFormulaireBis($numeroFormulaireBis)
    {
        $this->numeroFormulaireBis = $numeroFormulaireBis;

        return $this;
    }

    /**
     * Get numeroFormulaireBis
     *
     * @return string
     */
    public function getNumeroFormulaireBis()
    {
        return $this->numeroFormulaireBis;
    }

    /**
     * Set quartier
     *
     * @param string $quartier
     * @return Nif
     */
    public function setQuartier($quartier)
    {
        $this->quartier = $quartier;

        return $this;
    }

    /**
     * Get quartier
     *
     * @return string
     */
    public function getQuartier()
    {
        return $this->quartier;
    }

    /**
     * Set rue
     *
     * @param string $rue
     * @return Nif
     */
    public function setRue($rue)
    {
        $this->rue = $rue;

        return $this;
    }

    /**
     * Get rue
     *
     * @return string
     */
    public function getRue()
    {
        return $this->rue;
    }

    /**
     * Set dateObtentionNif
     *
     * @param \DateTime $dateObtentionNif
     * @return Nif
     */
    public function setDateObtentionNif($dateObtentionNif)
    {
        $this->dateObtentionNif = $dateObtentionNif;

        return $this;
    }

    /**
     * Get dateObtentionNif
     *
     * @return \DateTime
     */
    public function getDateObtentionNif()
    {
        return $this->dateObtentionNif;
    }

    /**
     * Set statut
     *
     * @param boolean $statut
     * @return Nif
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return boolean
     */
    public function getStatut()
    {
        return $this->statut;
    }
}
