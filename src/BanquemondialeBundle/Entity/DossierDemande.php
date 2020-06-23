<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DossierDemande
 *
 * @ORM\Table(name="dossierdemande")
 * @UniqueEntity("denominationSociale",message="denomination_exist")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\DossierDemandeRepository")
 */
class DossierDemande {

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
     * @ORM\Column(name="statusRetrait", type="boolean",nullable=true)
     */
    private $statusRetrait;

    /**
     * @return bool
     */
    public function getStatusRetrait()
    {
        return $this->statusRetrait;
    }

    /**
     * @param bool $statusRetrait
     */
    public function setStatusRetrait($statusRetrait)
    {
        $this->statusRetrait = $statusRetrait;
    }

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\TypeOperation")
     * @ORM\JoinColumn(name="idTypeOperation", referencedColumnName="id")
     */
    private $typeOperation;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\TypeDossier")
     * @ORM\JoinColumn(name="idTypeDossier", referencedColumnName="id")
     */
    private $typeDossier;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\FormeJuridique")
     * @ORM\JoinColumn(name="idFormeJuridique", referencedColumnName="id")
     */
    private $formeJuridique;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Pays")
     * @ORM\JoinColumn(name="idPays", referencedColumnName="id")
     */
    private $pays;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Region")
     * @ORM\JoinColumn(name="idRegion", referencedColumnName="id")
     */
    private $region;

    

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Prefecture")
     * @ORM\JoinColumn(name="idPrefecture", referencedColumnName="id")
     */
    private $prefecture;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\SousPrefectureCommune")
     * @ORM\JoinColumn(name="idSousPrefecture", referencedColumnName="id")
     */
    private $sousPrefecture;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Quartier")
     * @ORM\JoinColumn(name="idQuartier", referencedColumnName="id")
     */
    private $quartierCodifie;
    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\SecteurActivite")
     * @ORM\JoinColumn(name="idSecteurActivite", referencedColumnName="id")
     */
    private $secteurActivite;
    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\CategorieActivite")
     * @ORM\JoinColumn(name="idCategorieActivite", referencedColumnName="id")
     */
    private $categorie;

    /**
     * @ORM\ManyToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs")
     * @ORM\JoinColumn(name="idUtilisateur", referencedColumnName="id")
     */
    private $utilisateur;

    /**
     * @ORM\ManyToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs")
     * @ORM\JoinColumn(name="idUserDepot", referencedColumnName="id")
     */
    private $utilisateurDepot;

    /**
     * @ORM\ManyToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs")
     * @ORM\JoinColumn(name="idUserValidation", referencedColumnName="id")
     */
    private $utilisateurValidation;

    /**
     * @var string
     * @ORM\Column(name="denominationSociale", type="string", length=100, unique=true)
     */
    private $denominationSociale;

    /**
     * @var string 
     * @ORM\Column(name="nomCommercial", type="string", length=100,nullable=true)
     */
    private $nomCommercial;

    /**
     * @var string
     *
     * @ORM\Column(name="activiteSociale", type="string", length=255,nullable=true)
     */
    private $activiteSociale;

    /**
     * @var string
     *
     * @ORM\Column(name="activiteComplementaire", type="string", length=255,nullable=true)
     */
    private $activiteComplementaire;

    /**
     * @var string
     *
     * @ORM\Column(name="sigle", type="string", length=100,nullable=true)
     */
    private $sigle;

    /**
     * @var string
     *
     * @ORM\Column(name="enseigne", type="string", length=100,nullable=true)
     */
    private $enseigne;

    /**
     * @var string
     *
     * @ORM\Column(name="adresseSiege", type="string", length=100)
     */
    private $adresseSiege;

    /**
     * @var string
     *
     * @ORM\Column(name="adresseEtablissement", type="string", length=100,nullable=true)
     */
    private $adresseEtablissement;

    /**
     * @var int
     *
     * @ORM\Column(name="boitePostale", type="integer",nullable=true)
     */
    private $boitePostale;

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
     * @ORM\Column(name="email", type="string", length=100,nullable=true)
     *
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="enActivite", type="boolean")
     */
    private $enActivite;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\SecteurActivite")
     * @ORM\JoinColumn(name="idActiviteSecondaire", referencedColumnName="id")
     */
    private $activiteSecondaire;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\SecteurActivite")
     * @ORM\JoinColumn(name="idActiviteSecondaire2", referencedColumnName="id")
     */
    private $activiteSecondaire2;

    /**
     * @var interger
     *
     * @ORM\Column(name="nombreAction", type="integer", length=11,nullable=true)
     */
    private $nombreAction;

    /**
     * @var datetime
     *
     * @ORM\Column(name="dateDebut", type="datetime", length=11,nullable=true)
     */
    private $dateDebut;

    /**
     * @var interger
     *
     * @ORM\Column(name="nombreSalariePrevu", type="integer", length=11,nullable=true)
     */
    private $nombreSalariePrevu;

    /**
     * @var interger
     *
     * @ORM\Column(name="duree", type="integer", length=11,nullable=true)
     */
    private $duree;

    /**
     * @var float
     *
     * @ORM\Column(name="valeurNominale", type="float",nullable=true)
     */
    private $valeurNominale;

    /**
     * @var float
     *
     * @ORM\Column(name="capitalSocial", type="float",nullable=true)
     */
    private $capitalSocial;

    /**
     * @var float
     *
     * @ORM\Column(name="apportNumeraire", type="float",nullable=true)
     */
    private $apportNumeraire;

    /**
     * @var float
     *
     * @ORM\Column(name="apportNature", type="float",nullable=true)
     */
    private $apportNature;

    /** 	
     * @ORM\OneToMany(targetEntity="DocumentCollected",mappedBy="dossierDemande")
     */
    private $documentCollectes;

    /**
     * @var integer
     *
     * @ORM\Column(name="statut", type="integer",nullable=true)
     */
    private $statut;

    /**
     * @var integer
     *
     * @ORM\Column(name="statutValidation", type="integer",nullable=true)
     */
    private $statutValidation;

    /**
     * @var datetime
     *
     * @ORM\Column(name="dateCreation", type="datetime",nullable=true)
     */
    private $dateCreation;

    /**
     * @var datetime
     *
     * @ORM\Column(name="dateValidation", type="datetime",nullable=true)
     */
    private $dateValidation;

    /**
     * @var datetime
     *
     * @ORM\Column(name="dateDelivrance", type="datetime",nullable=true)
     */
    private $dateDelivrance;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroDossier", type="string", length=255,nullable=true)
     */
    private $numeroDossier;

    /**
     * @var string
     *
     * @ORM\Column(name="rccmSiege", type="string", length=100,nullable=true)
     */
    private $rccmSiege;

    /**
     * @var string
     *
     * @ORM\Column(name="codeFiscal", type="string", length=100,nullable=true)
     */
    private $codeFiscal;

    /**
     * @var string
     *
     * @ORM\Column(name="ninea", type="string", length=100,nullable=true)
     */
    private $ninea;

    /**
     * @var string
     *
     */
    private $gerant;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=200,nullable=true)
     */
    private $ville;

    /**
     * @var string 
     * @ORM\Column(name="motif", type="string", length=255,nullable=true)
     */
    private $motif;

    /**
     * @var string 
     * @ORM\Column(name="soussigne", type="string", length=100,nullable=true)
     */
    private $soussigne;

    /**
     * @var string 
     * @ORM\Column(name="fonctionSoussigne", type="string", length=100,nullable=true)
     */
    private $fonctionSoussigne;

    /**
     * @var string
     * @ORM\Column(name="quartier", type="string", length=100, nullable=true)
     */
    private $quartier;

    /**
     * @var string
     *
     * @ORM\Column(name="telephonePromoteur", type="string", length=20,nullable=true)
     * @Assert\Regex(pattern="/^(\((\+|00)[0-9]{2,5}\))([0-9 ]{1,20})$/",

     *     match=true,

     *     message="telephone_invalide")

     */
    private $telephonePromoteur;

    /**
     * @var string
     *
     * @ORM\Column(name="emailPromoteur", type="string", length=100,nullable=true)
     *
     */
    private $emailPromoteur;

    /**
     * @var integer
     *
     * @ORM\Column(name="statutValidationChefGreffe", type="integer",nullable=true)
     */
    private $statutValidationChefGreffe;

    /**
     * @var datetime
     *
     * @ORM\Column(name="dateValidationChefGreffe", type="datetime", nullable=true)
     */
    private $dateValidationChefGreffe;

    /**
     * @var string
     *
     */
    private $entreprise;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get region
     *
     * @return \BanquemondialeBundle\Entity\Region
     */
    public function getRegion() {
        return $this->region;
    }

    /**
     * Set region
     *
     * @param \BanquemondialeBundle\Entity\Region $region
     * @return DossierDemande
     */
    public function setRegion($region) {
        $this->region = $region;
    }   

    /**
     * Set denominationSociale
     *
     * @param string $denominationSociale
     * @return DossierDemande
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
     * Set nomCommercial
     *
     * @param string $nomCommercial
     * @return DossierDemande
     */
    public function setNomCommercial($nomCommercial) {
        $this->nomCommercial = $nomCommercial;

        return $this;
    }

    /**
     * Get nomCommercial
     *
     * @return string 
     */
    public function getNomCommercial() {
        return $this->nomCommercial;
    }

    /**
     * Set sigle
     *
     * @param string $sigle
     * @return DossierDemande
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
     * Set enseigne
     *
     * @param string $enseigne
     * @return DossierDemande
     */
    public function setEnseigne($enseigne) {
        $this->enseigne = $enseigne;

        return $this;
    }

    /**
     * Get enseigne
     *
     * @return string 
     */
    public function getEnseigne() {
        return $this->enseigne;
    }

    /**
     * Set adresseSiege
     *
     * @param string $adresseSiege
     * @return DossierDemande
     */
    public function setAdresseSiege($adresseSiege) {
        $this->adresseSiege = $adresseSiege;

        return $this;
    }

    /**
     * Get adresseSiege
     *
     * @return string 
     */
    public function getAdresseSiege() {
        return $this->adresseSiege;
    }

    /**
     * Set boitePostale
     *
     * @param integer $boitePostale
     * @return DossierDemande
     */
    public function setBoitePostale($boitePostale) {
        $this->boitePostale = $boitePostale;

        return $this;
    }

    /**
     * Get boitePostale
     *
     * @return integer 
     */
    public function getBoitePostale() {
        return $this->boitePostale;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return DossierDemande
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
     * Set email
     *
     * @param string $email
     * @return DossierDemande
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set nombreAction
     *
     * @param integer $nombreAction
     * @return DossierDemande
     */
    public function setNombreAction($nombreAction) {
        $this->nombreAction = $nombreAction;

        return $this;
    }

    /**
     * Get nombreAction
     *
     * @return integer 
     */
    public function getNombreAction() {
        return $this->nombreAction;
    }

    /**
     * Set valeurNominale
     *
     * @param  $valeurNominale
     * @return DossierDemande
     */
    public function setValeurNominale($valeurNominale) {
        $this->valeurNominale = $valeurNominale;

        return $this;
    }

    /**
     * Get valeurNominale
     *
     * @return  
     */
    public function getValeurNominale() {
        return $this->valeurNominale;
    }

    /**
     * Set capitalSocial
     *
     * @param  $capitalSocial
     * @return DossierDemande
     */
    public function setCapitalSocial($capitalSocial) {
        $this->capitalSocial = $capitalSocial;

        return $this;
    }

    /**
     * Get capitalSocial
     *
     * @return  
     */
    public function getCapitalSocial() {
        return $this->capitalSocial;
    }

    /**
     * Set apportNumeraire
     *
     * @param   $apportNumeraire
     * @return DossierDemande
     */
    public function setApportNumeraire($apportNumeraire) {
        $this->apportNumeraire = $apportNumeraire;

        return $this;
    }

    /**
     * Get apportNumeraire
     *
     * @return  
     */
    public function getApportNumeraire() {
        return $this->apportNumeraire;
    }

    /**
     * Set apportNature
     *
     * @param  $apportNature
     * @return DossierDemande
     */
    public function setApportNature($apportNature) {
        $this->apportNature = $apportNature;

        return $this;
    }

    /**
     * Get apportNature
     *
     * @return  
     */
    public function getApportNature() {
        return $this->apportNature;
    }

    /**
     * Set activiteSociale
     *
     * @param string $activiteSociale
     * @return DossierDemande
     */
    public function setActiviteSociale($activiteSociale) {
        $this->activiteSociale = $activiteSociale;

        return $this;
    }

    /**
     * Get activiteSociale
     *
     * @return string 
     */
    public function getActiviteSociale() {
        return $this->activiteSociale;
    }

    /**
     * Get secteurActivite
     *
     * @return \BanquemondialeBundle\Entity\SecteurActivite 
     */
    public function getSecteurActivite() {
        return $this->secteurActivite;
    }
/**
     * Set secteurActivite
     *
     * @param \BanquemondialeBundle\Entity\SecteurActivite $activitePrincipale
     * @return DossierDemande
     */
    public function setSecteurActivite(\BanquemondialeBundle\Entity\SecteurActivite $activitePrincipale = null) {
        $this->secteurActivite = $activitePrincipale;

        return $this;
    }
    /**
     * Set pays
     *
     * @param \BanquemondialeBundle\Entity\Pays $pays
     * @return DossierDemande
     */
    public function setPays(\BanquemondialeBundle\Entity\Pays $pays = null) {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return \BanquemondialeBundle\Entity\Pays 
     */
    public function getPays() {
        return $this->pays;
    }

    /**
     * Set typeOperation
     *
     * @param \BanquemondialeBundle\Entity\TypeOperation $typeOperation
     * @return DossierDemande
     */
    public function setTypeOperation(\BanquemondialeBundle\Entity\TypeOperation $typeOperation = null) {
        $this->typeOperation = $typeOperation;

        return $this;
    }

    /**
     * Get typeOperation
     *
     * @return \BanquemondialeBundle\Entity\TypeOperation 
     */
    public function getTypeOperation() {
        return $this->typeOperation;
    }

    /**
     * Set statut
     *
     * @param integer $statut
     * @return DossierDemande
     */
    public function setStatut($statut) {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return integer 
     */
    public function getStatut() {
        return $this->statut;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return DossierDemande
     */
    public function setDateCreation($dateCreation) {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation() {
        return $this->dateCreation;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return DossierDemande
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Set formeJuridique
     *
     * @param \BanquemondialeBundle\Entity\FormeJuridique $formeJuridique
     * @return DossierDemande
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
     * Set utilisateur
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $utilisateur
     * @return DossierDemande
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
     * Set dateDelivrance
     *
     * @param \DateTime $dateDelivrance
     * @return DossierDemande
     */
    public function setDateDelivrance($dateDelivrance) {
        $this->dateDelivrance = $dateDelivrance;

        return $this;
    }

    /**
     * Get dateDelivrance
     *
     * @return \DateTime 
     */
    public function getDateDelivrance() {
        return $this->dateDelivrance;
    }

    /**
     * Set numeroDossier
     *
     * @param string $numeroDossier
     * @return DossierDemande
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

    public function isOrangeCreateur() {
        if ($this->dateDelivrance != null)
            return false;
        $interval = $this->dateCreation->diff(new \Datetime());
        $days = $interval->days;
        $month = $interval->m;
        $y = $interval->y;
        $hours = $interval->format('%h');
        $minutes = $interval->format('%i');
        $diff = (($y * 12 * 30 * 3600) + ($month * 30 * 3600) + $days * 3600 + $hours * 60 + $minutes);
        if ($diff >= 3600 and $diff <= 7200)
            return true;
        else
            return false;
    }

    public function isRedCreateur() {
        if ($this->dateDelivrance != null)
            return false;
        $interval = $this->dateCreation->diff(new \Datetime());
        $days = $interval->days;
        $month = $interval->m;
        $y = $interval->y;
        $hours = $interval->format('%h');
        $minutes = $interval->format('%i');
        $diff = (($y * 12 * 30 * 3600) + ($month * 30 * 3600) + $days * 3600 + $hours * 60 + $minutes);
        if ($diff > 7200)
            return true;
        else
            return false;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->documentCollectes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add documentCollectes
     *
     * @param \BanquemondialeBundle\Entity\DocumentCollected $documentCollectes
     * @return DossierDemande
     */
    public function addDocumentCollecte(\BanquemondialeBundle\Entity\DocumentCollected $documentCollectes) {
        $this->documentCollectes[] = $documentCollectes;

        return $this;
    }

    /**
     * Remove documentCollectes
     *
     * @param \BanquemondialeBundle\Entity\DocumentCollected $documentCollectes
     */
    public function removeDocumentCollecte(\BanquemondialeBundle\Entity\DocumentCollected $documentCollectes) {
        $this->documentCollectes->removeElement($documentCollectes);
    }

    /**
     * Get documentCollectes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDocumentCollectes() {
        return $this->documentCollectes;
    }

    public function enAttenteModification() {
        foreach ($this->documentCollectes as $document) {
            if ($document->getStatutTraitement() and $document->getStatutTraitement()->getCode() == "DM")
                return true;
        }
        return false;
    }

    public function getMotifModification($pole) {
        foreach ($this->documentCollectes as $document) {
            if ($document->getPole() == $pole)
                return $document->getMotif();
        }
        return;
    }

    public function getDocumentCollectedPole($pole, $user) {
        $documents = array();
        foreach ($this->documentCollectes as $doc) {
            if ($doc->getPole() == $pole and ( $doc->getUtilisateur() == $user or $doc->getUtilisateur() == null))
                $documents [] = $doc;
        }
        return $documents;
    }

    /**
     * Set codeFiscal
     *
     * @param string $codeFiscal
     * @return DossierDemande
     */
    public function setCodeFiscal($codeFiscal) {
        $this->codeFiscal = $codeFiscal;

        return $this;
    }

    /**
     * Get codeFiscal
     *
     * @return string 
     */
    public function getCodeFiscal() {
        return $this->codeFiscal;
    }

    /**
     * Set ninea
     *
     * @param string $ninea
     * @return DossierDemande
     */
    public function setNinea($ninea) {
        $this->ninea = $ninea;

        return $this;
    }

    /**
     * Get ninea
     *
     * @return string 
     */
    public function getNinea() {
        return $this->ninea;
    }

    /**
     * Set typeDossier
     *
     * @param \BanquemondialeBundle\Entity\TypeDossier $typeDossier
     * @return DossierDemande
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
     * Set prefecture
     *
     * @param \BanquemondialeBundle\Entity\Prefecture $prefecture
     * @return DossierDemande
     */
    public function setPrefecture(\BanquemondialeBundle\Entity\Prefecture $prefecture = null) {
        $this->prefecture = $prefecture;

        return $this;
    }

    /**
     * Get prefecture
     *
     * @return \BanquemondialeBundle\Entity\Prefecture 
     */
    public function getPrefecture() {
        return $this->prefecture;
    }

    /**
     * Set sousPrefecture
     *
     * @param \BanquemondialeBundle\Entity\SousPrefectureCommune $sousPrefecture
     * @return DossierDemande
     */
    public function setSousPrefecture(\BanquemondialeBundle\Entity\SousPrefectureCommune $sousPrefecture = null) {
        $this->sousPrefecture = $sousPrefecture;

        return $this;
    }

    /**
     * Get sousPrefecture
     *
     * @return \BanquemondialeBundle\Entity\SousPrefectureCommune 
     */
    public function getSousPrefecture() {
        return $this->sousPrefecture;
    }
    

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return DossierDemande
     */
    public function setDateDebut($dateDebut) {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime 
     */
    public function getDateDebut() {
        return $this->dateDebut;
    }

    /**
     * Set nombreSalariePrevu
     *
     * @param integer $nombreSalariePrevu
     * @return DossierDemande
     */
    public function setNombreSalariePrevu($nombreSalariePrevu) {
        $this->nombreSalariePrevu = $nombreSalariePrevu;

        return $this;
    }

    /**
     * Get nombreSalariePrevu
     *
     * @return integer 
     */
    public function getNombreSalariePrevu() {
        return $this->nombreSalariePrevu;
    }

    /**
     * Set duree
     *
     * @param integer $duree
     * @return DossierDemande
     */
    public function setDuree($duree) {
        $this->duree = $duree;

        return $this;
    }

    /**
     * Get duree
     *
     * @return integer 
     */
    public function getDuree() {
        return $this->duree;
    }

    /**
     * Set activiteComplementaire
     *
     * @param string $activiteComplementaire
     * @return DossierDemande
     */
    public function setActiviteComplementaire($activiteComplementaire) {
        $this->activiteComplementaire = $activiteComplementaire;

        return $this;
    }

    /**
     * Get activiteComplementaire
     *
     * @return string 
     */
    public function getActiviteComplementaire() {
        return $this->activiteComplementaire;
    }

    /**
     * Set activiteSecondaire
     *
     * @param \BanquemondialeBundle\Entity\SecteurActivite $activiteSecondaire
     * @return DossierDemande
     */
    public function setActiviteSecondaire(\BanquemondialeBundle\Entity\SecteurActivite $activiteSecondaire = null) {
        $this->activiteSecondaire = $activiteSecondaire;

        return $this;
    }

    /**
     * Get activiteSecondaire
     *
     * @return \BanquemondialeBundle\Entity\SecteurActivite 
     */
    public function getActiviteSecondaire() {
        return $this->activiteSecondaire;
    }

    /**
     * Set utilisateurDepot
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $utilisateurDepot
     * @return DossierDemande
     */
    public function setUtilisateurDepot(\UtilisateursBundle\Entity\Utilisateurs $utilisateurDepot = null) {
        $this->utilisateurDepot = $utilisateurDepot;

        return $this;
    }

    /**
     * Get utilisateurDepot
     *
     * @return \UtilisateursBundle\Entity\Utilisateurs 
     */
    public function getUtilisateurDepot() {
        return $this->utilisateurDepot;
    }

    /**
     * Set gerant
     *
     * @param string $gerant
     * @return DossierDemande
     */
    public function setGerant($gerant) {
        $this->gerant = $gerant;

        return $this;
    }

    /**
     * Get gerant
     *
     * @return string 
     */
    public function getGerant() {
        return $this->gerant;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return DossierDemande
     */
    public function setVille($ville) {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string 
     */
    public function getVille() {
        return $this->ville;
    }

    /**
     * Set telephone2
     *
     * @param string $telephone2
     * @return DossierDemande
     */
    public function setTelephone2($telephone2) {
        $this->telephone2 = $telephone2;

        return $this;
    }

    /**
     * Get telephone2
     *
     * @return string 
     */
    public function getTelephone2() {
        return $this->telephone2;
    }

    /**
     * Set enActivite
     *
     * @param boolean $enActivite
     * @return DossierDemande
     */
    public function setEnActivite($enActivite) {
        $this->enActivite = $enActivite;

        return $this;
    }

    /**
     * Get enActivite
     *
     * @return boolean 
     */
    public function getEnActivite() {
        return $this->enActivite;
    }

    /**
     * Set activiteSecondaire2
     *
     * @param \BanquemondialeBundle\Entity\SecteurActivite $activiteSecondaire2
     * @return DossierDemande
     */
    public function setActiviteSecondaire2(\BanquemondialeBundle\Entity\SecteurActivite $activiteSecondaire2 = null) {
        $this->activiteSecondaire2 = $activiteSecondaire2;

        return $this;
    }

    /**
     * Get activiteSecondaire2
     *
     * @return \BanquemondialeBundle\Entity\SecteurActivite 
     */
    public function getActiviteSecondaire2() {
        return $this->activiteSecondaire2;
    }

    /**
     * Set adresseEtablissement
     *
     * @param string $adresseEtablissement
     * @return DossierDemande
     */
    public function setAdresseEtablissement($adresseEtablissement) {
        $this->adresseEtablissement = $adresseEtablissement;

        return $this;
    }

    /**
     * Get adresseEtablissement
     *
     * @return string 
     */
    public function getAdresseEtablissement() {
        return $this->adresseEtablissement;
    }

    /**
     * Set rccmSiege
     *
     * @param string $rccmSiege
     * @return DossierDemande
     */
    public function setRccmSiege($rccmSiege) {
        $this->rccmSiege = $rccmSiege;

        return $this;
    }

    /**
     * Get rccmSiege
     *
     * @return string 
     */
    public function getRccmSiege() {
        return $this->rccmSiege;
    }

    /**
     * Set motif
     *
     * @param string $motif
     * @return DossierDemande
     */
    public function setMotif($motif) {
        $this->motif = $motif;

        return $this;
    }

    /**
     * Get motif
     *
     * @return string 
     */
    public function getMotif() {
        return $this->motif;
    }

    /**
     * Set statutValidation
     *
     * @param integer $statutValidation
     * @return DossierDemande
     */
    public function setStatutValidation($statutValidation) {
        $this->statutValidation = $statutValidation;

        return $this;
    }

    /**
     * Get statutValidation
     *
     * @return integer 
     */
    public function getStatutValidation() {
        return $this->statutValidation;
    }

    /**
     * Set dateValidation
     *
     * @param \DateTime $dateValidation
     * @return DossierDemande
     */
    public function setDateValidation($dateValidation) {
        $this->dateValidation = $dateValidation;

        return $this;
    }

    /**
     * Get dateValidation
     *
     * @return \DateTime 
     */
    public function getDateValidation() {
        return $this->dateValidation;
    }

    /**
     * Set utilisateurValidation
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $utilisateurValidation
     * @return DossierDemande
     */
    public function setUtilisateurValidation(\UtilisateursBundle\Entity\Utilisateurs $utilisateurValidation = null) {
        $this->utilisateurValidation = $utilisateurValidation;

        return $this;
    }

    /**
     * Get utilisateurValidation
     *
     * @return \UtilisateursBundle\Entity\Utilisateurs 
     */
    public function getUtilisateurValidation() {
        return $this->utilisateurValidation;
    }

    /**
     * Set soussigne
     *
     * @param string $soussigne
     * @return DossierDemande
     */
    public function setSoussigne($soussigne) {
        $this->soussigne = $soussigne;

        return $this;
    }

    /**
     * Get soussigne
     *
     * @return string 
     */
    public function getSoussigne() {
        return $this->soussigne;
    }

    /**
     * Set fonctionSoussigne
     *
     * @param string $fonctionSoussigne
     * @return DossierDemande
     */
    public function setFonctionSoussigne($fonctionSoussigne) {
        $this->fonctionSoussigne = $fonctionSoussigne;

        return $this;
    }

    /**
     * Get fonctionSoussigne
     *
     * @return string 
     */
    public function getFonctionSoussigne() {
        return $this->fonctionSoussigne;
    }

    /**
     * Set quartier
     *
     * @param string $quartier
     * @return DossierDemande
     */
    public function setQuartier($quartier) {
        $this->quartier = $quartier;

        return $this;
    }

    /**
     * Get quartier
     *
     * @return string 
     */
    public function getQuartier() {
        return $this->quartier;
    }

    /**
     * Set telephonePromoteur
     *
     * @param string $telephonePromoteur
     * @return DossierDemande
     */
    public function setTelephonePromoteur($telephonePromoteur) {
        $this->telephonePromoteur = $telephonePromoteur;

        return $this;
    }

    /**
     * Get telephonePromoteur
     *
     * @return string 
     */
    public function getTelephonePromoteur() {
        return $this->telephonePromoteur;
    }

    /**
     * Set emailPromoteur
     *
     * @param string $emailPromoteur
     * @return DossierDemande
     */
    public function setEmailPromoteur($emailPromoteur) {
        $this->emailPromoteur = $emailPromoteur;

        return $this;
    }

    /**
     * Get emailPromoteur
     *
     * @return string 
     */
    public function getEmailPromoteur() {
        return $this->emailPromoteur;
    }

    /**
     * Set statutValidationChefGreffe
     *
     * @param integer $statutValidationChefGreffe
     * @return DossierDemande
     */
    public function setStatutValidationChefGreffe($statutValidationChefGreffe) {
        $this->statutValidationChefGreffe = $statutValidationChefGreffe;

        return $this;
    }

    /**
     * Get statutValidationChefGreffe
     *
     * @return integer 
     */
    public function getStatutValidationChefGreffe() {
        return $this->statutValidationChefGreffe;
    }

    /**
     * Set dateValidationChefGreffe
     *
     * @param \DateTime $dateValidationChefGreffe
     * @return DossierDemande
     */
    public function setDateValidationChefGreffe($dateValidationChefGreffe) {
        $this->dateValidationChefGreffe = $dateValidationChefGreffe;

        return $this;
    }

    /**
     * Get dateValidationChefGreffe
     *
     * @return \DateTime 
     */
    public function getDateValidationChefGreffe() {
        return $this->dateValidationChefGreffe;
    }
    function getEntreprise() {
        return $this->entreprise;
    }

    function setEntreprise($entreprise) {
        $this->entreprise = $entreprise;
    }



    /**
     * Set quartierCodifie
     *
     * @param \BanquemondialeBundle\Entity\Quartier $quartierCodifie
     * @return DossierDemande
     */
    public function setQuartierCodifie(\BanquemondialeBundle\Entity\Quartier $quartierCodifie = null)
    {
        $this->quartierCodifie = $quartierCodifie;

        return $this;
    }

    /**
     * Get quartierCodifie
     *
     * @return \BanquemondialeBundle\Entity\Quartier 
     */
    public function getQuartierCodifie()
    {
        return $this->quartierCodifie;
    }

    /**
     * Set categorie
     *
     * @param \BanquemondialeBundle\Entity\CategorieActivite $categorie
     * @return DossierDemande
     */
    public function setCategorie(\BanquemondialeBundle\Entity\CategorieActivite $categorie = null)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \BanquemondialeBundle\Entity\CategorieActivite 
     */
    public function getCategorie()
    {
        return $this->categorie;
    }
}
