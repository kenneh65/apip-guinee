<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DossierDemandeCommissionnaireAuCompte
 *
 * @ORM\Table(name="dossierDemandeCommissionnaireAuCompte")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\DossierDemandeCommissionnaireAuCompteRepository")
 */
class DossierDemandeCommissionnaireAuCompte
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
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\TypeFonctionCommissaire")
     * @ORM\JoinColumn(name="idFonction", referencedColumnName="id")    
     */
     private $fonction;
    
    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\DossierDemande")
     * @ORM\JoinColumn(name="idDossierDemande", referencedColumnName="id")    
     */
    private $dossierDemande;

   /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\CommissionnaireAuCompte")
     * @ORM\JoinColumn(name="idCommissionnaireAuCompte", referencedColumnName="id")    
     */
    private $commissionnaireAuCompte;
/**
     * @var string
     *     
     */
    private $prenom;
    /**
     * @var string
     *     
     */
    private $adresse;
    
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

    function getCommissionnaireAuCompte() {
        return $this->commissionnaireAuCompte;
    }

    function setDossierDemande($dossierDemande) {
        $this->dossierDemande = $dossierDemande;
    }

    function setCommissionnaireAuCompte($commissionnaireAuCompte) {
        $this->commissionnaireAuCompte = $commissionnaireAuCompte;
    }
        

    
    function getPrenom() {
        return $this->prenom;
    }

    function getAdresse() {
        return $this->adresse;
    }

    function setPrenom($prenom) {
        $this->prenom = $prenom;
    }

    function setAdresse($adresse) {
        $this->adresse = $adresse;
    }




    /**
     * Set fonction
     *
     * @param \BanquemondialeBundle\Entity\TypeFonctionCommissaire $fonction
     * @return DossierDemandeCommissionnaireAuCompte
     */
    public function setFonction(\BanquemondialeBundle\Entity\TypeFonctionCommissaire $fonction = null)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get fonction
     *
     * @return \BanquemondialeBundle\Entity\TypeFonctionCommissaire 
     */
    public function getFonction()
    {
        return $this->fonction;
    }
}
