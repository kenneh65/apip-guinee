<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActiviteAnterieure
 *
 * @ORM\Table(name="activiteanterieure")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\ActiviteAnterieureRepository")
 */
class ActiviteAnterieure
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
     * @ORM\Column(name="dateDebut", type="string", length=7)
     */
    private $dateDebut;

    /**
     * @var string
     *
     * @ORM\Column(name="dateFin", type="string", length=7)
     */
    private $dateFin;

    /**
     * @var string
     *
     * @ORM\Column(name="precedentRccm", type="string", length=20,nullable=true)
     */
    private $precedentRccm;

    /**
     * @var string
     *
     * @ORM\Column(name="natureActivite", type="string", length=255, nullable=true)
     */
    private $natureActivite;
    /**
     * @var string
     *
     * @ORM\Column(name="etablissementPrincipal", type="string", length=100, nullable=true)
     */
    private $etablissementPrincipal;
    /**
     * @var string
     *
     * @ORM\Column(name="etablissementSecondaire", type="string", length=255, nullable=true)
     */
    private $etablissementSecondaire;
    /**
     * @var string
     *
     * @ORM\Column(name="rccmEtabSecondaire", type="string", length=100, nullable=true)
     */
    private $rccmEtabSecondaire;
    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=200, nullable=true)
     */
    private $adresse;
    

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

    
    
    function getDossierDemande() {
        return $this->dossierDemande;
    }

    function setDossierDemande(\BanquemondialeBundle\Entity\DossierDemande $dossierDemande = null) {
        $this->dossierDemande = $dossierDemande;
    }

    /**
     * Set precedentRccm
     *
     * @param string $precedentRccm
     * @return ActiviteAnterieure
     */
    public function setPrecedentRccm($precedentRccm)
    {
        $this->precedentRccm = $precedentRccm;

        return $this;
    }

    /**
     * Get precedentRccm
     *
     * @return string 
     */
    public function getPrecedentRccm()
    {
        return $this->precedentRccm;
    }

    /**
     * Set natureActivite
     *
     * @param string $natureActivite
     * @return ActiviteAnterieure
     */
    public function setNatureActivite($natureActivite)
    {
        $this->natureActivite = $natureActivite;

        return $this;
    }

    /**
     * Get natureActivite
     *
     * @return string 
     */
    public function getNatureActivite()
    {
        return $this->natureActivite;
    }

    /**
     * Set rccmEtabSecondaire
     *
     * @param string $rccmEtabSecondaire
     * @return ActiviteAnterieure
     */
    public function setRccmEtabSecondaire($rccmEtabSecondaire)
    {
        $this->rccmEtabSecondaire = $rccmEtabSecondaire;

        return $this;
    }

    /**
     * Get rccmEtabSecondaire
     *
     * @return string 
     */
    public function getRccmEtabSecondaire()
    {
        return $this->rccmEtabSecondaire;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return ActiviteAnterieure
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
     * Set etablissementPrincipal
     *
     * @param string $etablissementPrincipal
     * @return ActiviteAnterieure
     */
    public function setEtablissementPrincipal($etablissementPrincipal)
    {
        $this->etablissementPrincipal = $etablissementPrincipal;

        return $this;
    }

    /**
     * Get etablissementPrincipal
     *
     * @return string 
     */
    public function getEtablissementPrincipal()
    {
        return $this->etablissementPrincipal;
    }

    /**
     * Set etablissementSecondaire
     *
     * @param string $etablissementSecondaire
     * @return ActiviteAnterieure
     */
    public function setEtablissementSecondaire($etablissementSecondaire)
    {
        $this->etablissementSecondaire = $etablissementSecondaire;

        return $this;
    }

    /**
     * Get etablissementSecondaire
     *
     * @return string 
     */
    public function getEtablissementSecondaire()
    {
        return $this->etablissementSecondaire;
    }

    

    /**
     * Set dateDebut
     *
     * @param string $dateDebut
     * @return ActiviteAnterieure
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return string 
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param string $dateFin
     * @return ActiviteAnterieure
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return string 
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }
}
