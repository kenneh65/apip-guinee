<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * FormulaireDelivre
 *
 * @ORM\Table(name="formulairedelivre")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\FormulaireDelivreRepository")
 */
class FormulaireDelivre
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
     * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\Pole")
     * @ORM\JoinColumn(name="idPole", referencedColumnName="id")
     */
    private $pole;

/**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\DossierDemande")
      * @ORM\JoinColumn(name="idDossierDemande", referencedColumnName="id")
      */
    private $dossierDemande;
	
	/**
     * @var string
     *
     * @ORM\Column(name="nomFichier", type="string", length=50,nullable=true)
     */
    private $nomFichier;
	
	/**
     * @var datetime
     *
     * @ORM\Column(name="dateCreation", type="datetime",nullable=true)
     */
    private $dateCreation;
	
	
	/**
     * @var int
     *
     * @ORM\Column(name="numero", type="integer", options={"default" : 1})
     */
    private $numero =1;
	
	
	/**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\LibelleFormulaireDelivre")
     * @ORM\JoinColumn(name="idLibelleFormulaireDelivre", referencedColumnName="id")
     */
    private $libelleFormulaireDelivre;
	
   /**
     * @var boolean
     *@ORM\Column(name="estRetire",type="boolean", nullable=true)
     */
    private $estRetire;

     /**
     * @var datetime
     * @ORM\Column(name="dateRetrait",type="datetime", nullable=true)
     */
    private $dateRetrait;
	
    /**
     * @var string
     *
     * @ORM\Column(name="beneficiaire", type="string", length=150,nullable=true)
     */
    private $beneficiaire;
	
    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=20,nullable=true)
     * @Assert\Regex(pattern="/^(\((\+|00)[0-9]{2,5}\))([0-9 ]{1,20})$/",

     *     match=true,

     *     message="telephone_invalide")

     */
    private $telephone;
	
	/**
     * @ORM\ManyToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs")
     * @ORM\JoinColumn(name="idUtilisateurRetrait", referencedColumnName="id")
     */
    private $utilisateurRetrait; 
	

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
     * Set id
     *
     * @param integer $id
     * @return FormulaireDelivre
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

	
    

    /**
     * Set dossierDemande
     *
     * @param \BanquemondialeBundle\Entity\DossierDemande $dossierDemande
     * @return FormulaireDelivre
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
     * Set pole
     *
     * @param \ParametrageBundle\Entity\Pole $pole
     * @return FormulaireDelivre
     */
    public function setPole(\ParametrageBundle\Entity\Pole $pole = null)
    {
        $this->pole = $pole;

        return $this;
    }

    /**
     * Get pole
     *
     * @return \ParametrageBundle\Entity\Pole 
     */
    public function getPole()
    {
        return $this->pole;
    }
	
	
	/**
     * Set nomFichier
     *
     * @param string $nomFichier
     * @return FormulaireDelivre
     */
    public function setNomFichier($nomFichier)
    {
        $this->nomFichier = $nomFichier;

        return $this;
    }

    /**
     * Get nomFichier
     *
     * @return string 
     */
    public function getNomFichier()
    {
        return $this->nomFichier;
    }
	
	
	
    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return FormulaireDelivre
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }
	
	
	/**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }
	
	/**
     * Set numero
     *
     * @param integer $numero
     * @return FormulaireDelivre
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

	

    /**
     * Set libelleFormulaireDelivre
     *
     * @param \BanquemondialeBundle\Entity\LibelleFormulaireDelivre $libelleFormulaireDelivre
     * @return FormulaireDelivre
     */
    public function setLibelleFormulaireDelivre(\BanquemondialeBundle\Entity\LibelleFormulaireDelivre $libelleFormulaireDelivre = null)
    {
        $this->libelleFormulaireDelivre = $libelleFormulaireDelivre;

        return $this;
    }

    /**
     * Get libelleFormulaireDelivre
     *
     * @return \BanquemondialeBundle\Entity\LibelleFormulaireDelivre 
     */
    public function getLibelleFormulaireDelivre()
    {
        return $this->libelleFormulaireDelivre;
    }
	
	/**
     * Get estRetire
     *
     * @return boolean 
     */
	public function getEstRetire()
    {
        return $this->estRetire;
    }
	
	/**
     * Set estRetire
     *
     * @param boolean $estRetire
     * @return DocumentCollected
     */
    public function setEstRetire($estRetire)
    {
        $this->estRetire = $estRetire;

        return $this;
    }
	
    /**
     * Set dateRetrait
     *
     * @param \DateTime $dateRetrait
     * @return DocumentCollected
     */
    public function setDateRetrait($dateRetrait)
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    /**
     * Get dateRetrait
     *
     * @return \DateTime 
     */
    public function getDateRetrait()
    {
        return $this->dateRetrait;
    }
	
    /**
     * Set beneficiaire
     *
     * @param string $beneficiaire
     * @return DocumentCollected
     */
    public function setBeneficiaire($beneficiaire) {
        $this->beneficiaire = $beneficiaire;

        return $this;
    }

    /**
     * Get beneficiaire
     *
     * @return string 
     */
    public function getBeneficiaire() {
        return $this->beneficiaire;
    }	
	
    /**
     * Set telephone
     *
     * @param string $telephone
     * @return documentcollected
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }
	
	
    /**
     * Set utilisateurRetrait
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $utilisateurRetrait
     * @return DocumentCollected
     */
    public function setUtilisateurRetrait(\UtilisateursBundle\Entity\Utilisateurs $utilisateurRetrait = null)
    {
        $this->utilisateurRetrait = $utilisateurRetrait;

        return $this;
    }

    /**
     * Get utilisateurRetrait
     *
     * @return \UtilisateursBundle\Entity\Utilisateurs 
     */
    public function getUtilisateurRetrait()
    {
        return $this->utilisateurRetrait;
    }
}
