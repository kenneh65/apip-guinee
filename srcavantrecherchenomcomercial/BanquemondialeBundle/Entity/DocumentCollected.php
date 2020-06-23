<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DocumentCollected
 *
 * @ORM\Table(name="documentcollected")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\DocumentCollectedRepository")
 */
class DocumentCollected {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\StatutTraitement")
     * @ORM\JoinColumn(name="idStatutTraitement", referencedColumnName="id", nullable=true)
     */
    private $statutTraitement;

    /**
     * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\Pole")
     * @ORM\JoinColumn(name="idPole", referencedColumnName="id")
     */
    private $pole;

    /** 	
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\DossierDemande",inversedBy="documentCollectes")
     * @ORM\JoinColumn(name="idDossierDemande", referencedColumnName="id")
     */
    private $dossierDemande;

    /**
     * @var string
     *
     * @ORM\Column(name="motif", type="string", length=255,nullable=true)
     */
    private $motif;

    /**
     * @var datetime
     * @ORM\Column(name="dateSoumission",type="datetime", nullable=true)
     */
    private $dateSoumission;
    
     /**
     * @var datetime
     * @ORM\Column(name="dateDelivrance",type="datetime", nullable=true)
     */
    private $dateDelivrance;

    /**
     * @var int
     *
     * @ORM\Column(name="ordre", type="integer",nullable=true)
     */
    private $ordre;
	
     
    /**
     * @ORM\ManyToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs")
     * @ORM\JoinColumn(name="idUtilisateur", referencedColumnName="id")
     */
    private $utilisateur; 
    
    /**
     * @var boolean
     *
     */
    private $cocher;
	
    /**
     * @var datetime
     * @ORM\Column(name="dateDerniereModification",type="datetime", nullable=true)
     */
    private $dateDerniereModification;

	
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return DocumentCollected
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Set statutTraitement
     *
     * @param \ParametrageBundle\Entity\StatutTraitement $statutTraitement
     * @return DocumentCollected
     */
    public function setStatutTraitement(\BanquemondialeBundle\Entity\StatutTraitement $statutTraitement = null) {
        $this->statutTraitement = $statutTraitement;

        return $this;
    }

    /**
     * Get statutTraitement
     *
     * @return \BanquemondialeBundle\Entity\StatutTraitement 
     */
    public function getStatutTraitement() {
        return $this->statutTraitement;
    }

    /**
     * Set dossierDemande
     *
     * @param \BanquemondialeBundle\Entity\DossierDemande $dossierDemande
     * @return DocumentCollected
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
     * Set pole
     *
     * @param \ParametrageBundle\Entity\Pole $pole
     * @return DocumentCollected
     */
    public function setPole(\ParametrageBundle\Entity\Pole $pole = null) {
        $this->pole = $pole;

        return $this;
    }

    /**
     * Get pole
     *
     * @return \ParametrageBundle\Entity\Pole 
     */
    public function getPole() {
        return $this->pole;
    }

    /**
     * Set motif
     *
     * @param string $motif
     * @return Document
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
     * Set ordre
     *
     * @param integer $ordre
     * @return DocumentCollected
     */
    public function setOrdre($ordre) {
        $this->ordre = $ordre;

        return $this;
    }

    
    /**
     * Get ordre
     *
     * @return integer 
     */
    public function getOrdre() {
        return $this->ordre;
    }
	
	


    /**
     * Set dateSoumission
     *
     * @param \DateTime $dateSoumission
     * @return DocumentCollected
     */
    public function setDateSoumission($dateSoumission) {
        $this->dateSoumission = $dateSoumission;

        return $this;
    }

    /**
     * Get dateSoumission
     *
     * @return \DateTime 
     */
    public function getDateSoumission() {
        return $this->dateSoumission;
    }

    public function isOrangePole() {
        if($this->dateSoumission==null)
            return false;
        $interval = $this->dateSoumission->diff(new \Datetime());
        $days = $interval->days;
        $month = $interval->m;
        $y = $interval->y;
        $hours = $interval->format('%h');
        $minutes = $interval->format('%i');
        $diff = (($y * 12 * 30 * 3600) + ($month * 30 * 3600) + $days * 3600 + $hours * 60 + $minutes);
        if($diff>=120 and $diff<=240)
            return true;
        else
            return false;
    }
    
     public function isRedPole() {
         if($this->dateSoumission==null)
            return false;
        $interval = $this->dateSoumission->diff(new \Datetime());
        $days = $interval->days;
        $month = $interval->m;
        $y = $interval->y;
        $hours = $interval->format('%h');
        $minutes = $interval->format('%i');
        $diff = (($y * 12 * 30 * 3600) + ($month * 30 * 3600) + $days * 3600 + $hours * 60 + $minutes);
        if($diff>240)
            return true;
        else
            return false;
    }

    /**
     * Set dateDelivrance
     *
     * @param \DateTime $dateDelivrance
     * @return DocumentCollected
     */
    public function setDateDelivrance($dateDelivrance)
    {
        $this->dateDelivrance = $dateDelivrance;

        return $this;
    }

    /**
     * Get dateDelivrance
     *
     * @return \DateTime 
     */
    public function getDateDelivrance()
    {
        return $this->dateDelivrance;
    }
	
     public function getDuree() {
         if($this->dateDelivrance==null)
            $time2=new \Datetime();
         else
             $time2 = $this->dateDelivrance;
         if($this->dateSoumission == null)
             return '-';
        $interval = $this->dateSoumission->diff($time2);
          $days = $interval->days;
        $month = $interval->m;
        $y = $interval->y;
        $hours=$interval->h;
         $diff = (($y * 365) + ($month * 30) + $days+ ($hours/24) + ($interval->i/ 1440) +($interval->s/86400));        
        return sprintf('%.2f',$diff);
    }

    /**
     * Set utilisateur
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $utilisateur
     * @return DocumentCollected
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
    
    public function getCocher()
    {
        return $this->cocher;
    }
    public function setCocher( $cocher = null)
    {
        $this->cocher = $cocher;

        return $this;
    }	
	

    /**
     * Set dateDerniereModification
     *
     * @param \DateTime $dateDerniereModification
     * @return DocumentCollected
     */
    public function setDateDerniereModification($dateDerniereModification)
    {
        $this->dateDerniereModification = $dateDerniereModification;

        return $this;
    }

    /**
     * Get dateDerniereModification
     *
     * @return \DateTime 
     */
    public function getDateDerniereModification()
    {
        return $this->dateDerniereModification;
    }
}
