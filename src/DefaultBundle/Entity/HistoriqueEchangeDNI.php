<?php

namespace DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistoriqueEchangeDNI
 *
 * @ORM\Table(name="historiqueechangedni")
 * @ORM\Entity(repositoryClass="DefaultBundle\Repository\HistoriqueEchangeDNIRepository")
 */
class HistoriqueEchangeDNI
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
     * @ORM\Column(name="NumeroDossier", type="string", length=20)
     */
    private $numeroDossier;

    /**
     * @var bool
     *
     * @ORM\Column(name="statut", type="boolean", nullable=true)
     */
    private $statut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateEnvoiRccm", type="datetime")
     */
    private $dateEnvoiRccm;

    /**
     * @var string
     *
     * @ORM\Column(name="contenuEnvoi", type="text",  nullable=true)
     */
    private $contenuEnvoi;
    
    /**
     * @var string
     *
     * @ORM\Column(name="contenuDataRecu", type="text", nullable=true)
     */
    private $contenuDataRecu;
    /**
     * @var string
     *
     * @ORM\Column(name="codeRetourDNI", type="string", length=10, nullable=true)
     */
    private $codeRetourDNI;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateReceptionDonneeNIF", type="datetime",nullable=true)
     */
    private $dateReceptionDonneeNIF;
    
    /**
     * @var string
     *
     * @ORM\Column(name="codeReponseDNI", type="string", length=10, nullable=true)
     */
    private $codeRetourSynergui;

    /** 	
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\DossierDemande")
     * @ORM\JoinColumn(name="idDossierDemande", referencedColumnName="id")
     */
    private $dossierDemande;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nomFichierEnvoye", type="string", length=20,nullable=true)
     */
    private $nomFichierEnvoye;
    
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
     * Set numeroDossier
     *
     * @param string $numeroDossier
     * @return HistoriqueEchangeDNI
     */
    public function setNumeroDossier($numeroDossier)
    {
        $this->numeroDossier = $numeroDossier;

        return $this;
    }

    /**
     * Get numeroDossier
     *
     * @return string 
     */
    public function getNumeroDossier()
    {
        return $this->numeroDossier;
    }

    /**
     * Set dateEnvoiRccm
     *
     * @param \DateTime $dateEnvoiRccm
     * @return HistoriqueEchangeDNI
     */
    public function setDateEnvoiRccm($dateEnvoiRccm)
    {
        $this->dateEnvoiRccm = $dateEnvoiRccm;

        return $this;
    }

    /**
     * Get dateEnvoiRccm
     *
     * @return \DateTime 
     */
    public function getDateEnvoiRccm()
    {
        return $this->dateEnvoiRccm;
    }

    /**
     * Set contenuEnvoi
     *
     * @param string $contenuEnvoi
     * @return HistoriqueEchangeDNI
     */
    public function setContenuEnvoi($contenuEnvoi)
    {
        $this->contenuEnvoi = $contenuEnvoi;

        return $this;
    }

    /**
     * Get contenuEnvoi
     *
     * @return string 
     */
    public function getContenuEnvoi()
    {
        return $this->contenuEnvoi;
    }

    /**
     * Set codeRetourDNI
     *
     * @param string $codeRetourDNI
     * @return HistoriqueEchangeDNI
     */
    public function setCodeRetourDNI($codeRetourDNI)
    {
        $this->codeRetourDNI = $codeRetourDNI;

        return $this;
    }

    /**
     * Get codeRetourDNI
     *
     * @return string 
     */
    public function getCodeRetourDNI()
    {
        return $this->codeRetourDNI;
    }

    /**
     * Set dateReceptionDonneeNIF
     *
     * @param \DateTime $dateReceptionDonneeNIF
     * @return HistoriqueEchangeDNI
     */
    public function setDateReceptionDonneeNIF($dateReceptionDonneeNIF)
    {
        $this->dateReceptionDonneeNIF = $dateReceptionDonneeNIF;

        return $this;
    }

    /**
     * Get dateReceptionDonneeNIF
     *
     * @return \DateTime 
     */
    public function getDateReceptionDonneeNIF()
    {
        return $this->dateReceptionDonneeNIF;
    }

    /**
     * Set codeRetourSynergui
     *
     * @param string $codeRetourSynergui
     * @return HistoriqueEchangeDNI
     */
    public function setCodeRetourSynergui($codeRetourSynergui)
    {
        $this->codeRetourSynergui = $codeRetourSynergui;

        return $this;
    }

    /**
     * Get codeRetourSynergui
     *
     * @return string 
     */
    public function getCodeRetourSynergui()
    {
        return $this->codeRetourSynergui;
    }

    /**
     * Set dossierDemande
     *
     * @param \BanquemondialeBundle\Entity\DossierDemande $dossierDemande
     * @return HistoriqueEchangeDNI
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
     * Set nomFichierEnvoye
     *
     * @param string $nomFichierEnvoye
     * @return HistoriqueEchangeDNI
     */
    public function setNomFichierEnvoye($nomFichierEnvoye)
    {
        $this->nomFichierEnvoye = $nomFichierEnvoye;

        return $this;
    }

    /**
     * Get nomFichierEnvoye
     *
     * @return string 
     */
    public function getNomFichierEnvoye()
    {
        return $this->nomFichierEnvoye;
    }

    /**
     * Set contenuDataRecu
     *
     * @param string $contenuDataRecu
     * @return HistoriqueEchangeDNI
     */
    public function setContenuDataRecu($contenuDataRecu)
    {
        $this->contenuDataRecu = $contenuDataRecu;

        return $this;
    }

    /**
     * Get contenuDataRecu
     *
     * @return string 
     */
    public function getContenuDataRecu()
    {
        return $this->contenuDataRecu;
    }

    /**
     * Set statut
     *
     * @param boolean $statut
     * @return HistoriqueEchangeDNI
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
