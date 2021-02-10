<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Quittance
 *
 * @ORM\Table(name="Quittance")
 * @UniqueEntity("numeroQuittance",message="numero_quittance_existe")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\QuittanceRepository")
 */
class Quittance {

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
     * @ORM\Column(name="numeroDossier", type="string", length=100)
     */
    private $numeroDossier;

    /**
     * @var string
     *
     * @ORM\Column(name="denominationSociale", type="string", length=100)
     */
    private $denominationSociale;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\FormeJuridique")
     * @ORM\JoinColumn(name="idFormeJuridique", referencedColumnName="id")
     */
    private $formeJuridique;

    /**
     * @var float
     * @Assert\Range(min = 1)
     * @ORM\Column(name="montantTotalFacture", type="float", nullable=true)
     */
    private $montantTotalFacture;

    /**
     * @var float
     * @Assert\Range(min = 1)
     * @ORM\Column(name="montantVerse", type="float", nullable=true)
     */
    private $montantVerse;

    /**
     * @var float
     * @Assert\Range(min = 1)
     * @ORM\Column(name="montantRestant", type="float", nullable=true)
     */
    private $montantRestant;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\ModePaiement")
     * @ORM\JoinColumn(name="idModePaiement", referencedColumnName="id", nullable=true)
     */
    private $modePaiement;

    /**
     * @var string
     * 
     * @ORM\Column(name="numeroQuittance", type="string", length=250, unique=true, nullable=true)
     */
    private $numeroQuittance;

    /**
     * @var string
     * 
     * @ORM\Column(name="serie", type="string", length=250, nullable=true)
     */
    private $serie;

    /**
     * @var string
     * 
     * @ORM\Column(name="numeroVolume", type="string", length=250, nullable=true)
     */
    private $numeroVolume;

    /**
     * @var string
     * 
     * @ORM\Column(name="refTitreRecette", type="string", length=250, nullable=true)
     */
    private $refTitreRecette;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     *
     * @ORM\Column(name="dateFacturation", type="datetime")
     */
    private $dateFacturation;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     *
     * @ORM\Column(name="datePaiement", type="datetime", nullable=true)
     */
    private $datePaiement;

    /**
     * @ORM\ManyToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs")
     * @ORM\JoinColumn(name="idUtilisateur", referencedColumnName="id", nullable=true)
     */
    private $utilisateur;

    /**
     * @var int
     *
     * @ORM\Column(name="tranche", type="integer")
     */
    private $tranche;

    /** 	
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\DossierDemande")
     * @ORM\JoinColumn(name="idDossierDemande", referencedColumnName="id")
     */
    private $dossierDemande;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\TypeDossier")
     * @ORM\JoinColumn(name="idTypeDossier", referencedColumnName="id", nullable=true)
     */
    private $typeDossier;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\NatureRecette")
     * @ORM\JoinColumn(name="idNatureRecette", referencedColumnName="id", nullable=true)
     */
    private $natureRecette;

    /**
     * @var integer
     *
     * @ORM\Column(name="statut", type="integer",nullable=true)
     */
    private $statut;
    
     /**
     * @var string
     * 
     * @ORM\Column(name="motif", type="string", length=250, nullable=true)
     */
    private $motif;
    
    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\SousPrefectureCommune")
     * @ORM\JoinColumn(name="idSousPrefecture", referencedColumnName="id")
     */
    private $sousPrefecture;
	
	
	/**
     * @var bool
     *
     * @ORM\Column(name="isPaid", type="boolean", options={"default":false})
     */
    private $isPaid;
	

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set denominationSociale
     *
     * @param string $denominationSociale
     * @return Quittance
     */
    public function setDenominationSociale($denominationSociale) {
        $this->denominationSociale = $denominationSociale;

        return $this;
    }

    /**
     * Get denominationSociale
     *
     * @return string 
     */
    public function getDenominationSociale() {
        return $this->denominationSociale;
    }

    /**
     * Set serie
     *
     * @param string $serie
     * @return Quittance
     */
    public function setSerie($serie) {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return string 
     */
    public function getSerie() {
        return $this->serie;
    }

    /**
     * Set numeroVolume
     *
     * @param string $numeroVolume
     * @return Quittance
     */
    public function setNumeroVolume($numeroVolume) {
        $this->numeroVolume = $numeroVolume;

        return $this;
    }

    /**
     * Get numeroVolume
     *
     * @return string 
     */
    public function getNumeroVolume() {
        return $this->numeroVolume;
    }

    /**
     * Set refTitreRecette
     *
     * @param string $refTitreRecette
     * @return Quittance
     */
    public function setRefTitreRecette($refTitreRecette) {
        $this->refTitreRecette = $refTitreRecette;

        return $this;
    }

    /**
     * Get refTitreRecette
     *
     * @return string 
     */
    public function getRefTitreRecette() {
        return $this->refTitreRecette;
    }

    /**
     * Set montantTotalFacture
     *
     * @param float $montantTotalFacture
     * @return Quittance
     */
    public function setMontantTotalFacture($montantTotalFacture) {
        $this->montantTotalFacture = $montantTotalFacture;

        return $this;
    }

    /**
     * Get montantTotalFacture
     *
     * @return float 
     */
    public function getMontantTotalFacture() {
        return $this->montantTotalFacture;
    }

    /**
     * Set montantVerse
     *
     * @param float $montantVerse
     * @return Quittance
     */
    public function setMontantVerse($montantVerse) {
        $this->montantVerse = $montantVerse;

        return $this;
    }

    /**
     * Get montantVerse
     *
     * @return float 
     */
    public function getMontantVerse() {
        return $this->montantVerse;
    }

    /**
     * Set numeroQuittance
     *
     * @param string $numeroQuittance
     * @return Quittance
     */
    public function setNumeroQuittance($numeroQuittance) {
        $this->numeroQuittance = $numeroQuittance;

        return $this;
    }

    /**
     * Get numeroQuittance
     *
     * @return string 
     */
    public function getNumeroQuittance() {
        return $this->numeroQuittance;
    }

    /**
     * Set datePaiement
     *
     * @param \DateTime $datePaiement
     * @return Quittance
     */
    public function setDatePaiement($datePaiement) {
        $this->datePaiement = $datePaiement;
       // die(dump($datePaiement));
        return $this;
    }

    /**
     * Get datePaiement
     *
     * @return \DateTime 
     */
    public function getDatePaiement() {
        return $this->datePaiement;
    }

    /**
     * Set formeJuridique
     *
     * @param \BanquemondialeBundle\Entity\FormeJuridique $formeJuridique
     * @return Quittance
     */
    public function setFormeJuridique(\BanquemondialeBundle\Entity\FormeJuridique $formeJuridique = null) {
        $this->formeJuridique = $formeJuridique;

        return $this;
    }

    /**
     * Get formeJuridique
     *
     * @return \BanquemondialeBundle\Entity\FormeJuridique 
     */
    public function getFormeJuridique() {
        return $this->formeJuridique;
    }

    /**
     * Set modePaiement
     *
     * @param \BanquemondialeBundle\Entity\ModePaiement $modePaiement
     * @return Quittance
     */
    public function setModePaiement(\BanquemondialeBundle\Entity\ModePaiement $modePaiement = null) {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    /**
     * Get modePaiement
     *
     * @return \BanquemondialeBundle\Entity\ModePaiement 
     */
    public function getModePaiement() {
        return $this->modePaiement;
    }

    /**
     * Set numeroDossier
     *
     * @param string $numeroDossier
     * @return Quittance
     */
    public function setNumeroDossier($numeroDossier) {
        $this->numeroDossier = $numeroDossier;

        return $this;
    }

    /**
     * Get numeroDossier
     *
     * @return string 
     */
    public function getNumeroDossier() {
        return $this->numeroDossier;
    }

    /**
     * Set utilisateur
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $utilisateur
     * @return Quittance
     */
    public function setUtilisateur(\UtilisateursBundle\Entity\Utilisateurs $utilisateur = null) {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \UtilisateursBundle\Entity\Utilisateurs 
     */
    public function getUtilisateur() {
        return $this->utilisateur;
    }

    /**
     * Set montantRestant
     *
     * @param float $montantRestant
     * @return Quittance
     */
    public function setMontantRestant($montantRestant) {
        $this->montantRestant = $montantRestant;

        return $this;
    }

    /**
     * Get montantRestant
     *
     * @return float 
     */
    public function getMontantRestant() {
        return $this->montantRestant;
    }

    /**
     * Set dateFacturation
     *
     * @param \DateTime $dateFacturation
     * @return Quittance
     */
    public function setDateFacturation($dateFacturation) {
        $this->dateFacturation = $dateFacturation;

        return $this;
    }

    /**
     * Get dateFacturation
     *
     * @return \DateTime 
     */
    public function getDateFacturation() {
        return $this->dateFacturation;
    }

    /**
     * Set tranche
     *
     * @param integer $tranche
     * @return Quittance
     */
    public function setTranche($tranche) {
        $this->tranche = $tranche;

        return $this;
    }

    /**
     * Get tranche
     *
     * @return integer 
     */
    public function getTranche() {
        return $this->tranche;
    }

    /**
     * Set dossierDemande
     *
     * @param \BanquemondialeBundle\Entity\DossierDemande $dossierDemande
     * @return Quittance
     */
    public function setDossierDemande(\BanquemondialeBundle\Entity\DossierDemande $dossierDemande = null) {
        $this->dossierDemande = $dossierDemande;

        return $this;
    }

    /**
     * Get dossierDemande
     *
     * @return \BanquemondialeBundle\Entity\DossierDemande 
     */
    public function getDossierDemande() {
        return $this->dossierDemande;
    }

    /**
     * Set typeDossier
     *
     * @param \BanquemondialeBundle\Entity\TypeDossier $typeDossier
     * @return Quittance
     */
    public function setTypeDossier(\BanquemondialeBundle\Entity\TypeDossier $typeDossier = null) {
        $this->typeDossier = $typeDossier;

        return $this;
    }

    /**
     * Get typeDossier
     *
     * @return \BanquemondialeBundle\Entity\TypeDossier 
     */
    public function getTypeDossier() {
        return $this->typeDossier;
    }

    /**
     * Set natureRecette
     *
     * @param \BanquemondialeBundle\Entity\NatureRecette $natureRecette
     * @return Quittance
     */
    public function setNatureRecette(\BanquemondialeBundle\Entity\NatureRecette $natureRecette = null) {
        $this->natureRecette = $natureRecette;

        return $this;
    }

    /**
     * Get natureRecette
     *
     * @return \BanquemondialeBundle\Entity\NatureRecette 
     */
    public function getNatureRecette() {
        return $this->natureRecette;
    }


    /**
     * Set statut
     *
     * @param integer $statut
     * @return Quittance
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return integer 
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set motif
     *
     * @param string $motif
     * @return Quittance
     */
    public function setMotif($motif)
    {
        $this->motif = $motif;

        return $this;
    }

    /**
     * Get motif
     *
     * @return string 
     */
    public function getMotif()
    {
        return $this->motif;
    }

    /**
     * Set sousPrefecture
     *
     * @param \BanquemondialeBundle\Entity\SousPrefectureCommune $sousPrefecture
     * @return Quittance
     */
    public function setSousPrefecture(\BanquemondialeBundle\Entity\SousPrefectureCommune $sousPrefecture = null)
    {
        $this->sousPrefecture = $sousPrefecture;

        return $this;
    }

    /**
     * Get sousPrefecture
     *
     * @return \BanquemondialeBundle\Entity\SousPrefectureCommune 
     */
    public function getSousPrefecture()
    {
        return $this->sousPrefecture;
    }
	


    /**
     * Set isPaid
     *
     * @param boolean $isPaid
     * @return Quittance
     */
    public function setIsPaid($isPaid)
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    /**
     * Get isPaid
     *
     * @return boolean 
     */
    public function getIsPaid()
    {
        return $this->isPaid;
    }
}
