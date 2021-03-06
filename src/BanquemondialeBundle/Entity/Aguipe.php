<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Aguipe
 *
 * @ORM\Table(name="aguipe")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\AguipeRepository")
 */
class Aguipe
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
     * @ORM\Column(name="en_activite", type="boolean")
     */
    private $enActivite;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebutActivite", type="datetime", nullable=true)
     */
    private $dateDebutActivite;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateImmatriculation", type="datetime", nullable=true)
     */
    private $dateImmatriculation;
    
    /**
     * @var string
     *
     * @ORM\Column(name="numeroImmatriculation", type="string", length=30, nullable=true,unique=true)
     */
    private $numeroImmatriculation;

    private $nombreEmployeActuel;
    /**
     * @var int
     *
     * @ORM\Column(name="nombreEmployeGuineen", type="integer")
     */
    private $nombreEmployeGuineen;
    

    /**
     * @var int
     *
     * @ORM\Column(name="nombreEmployeEtranger", type="integer", nullable=true)
     */
    private $nombreEmployeEtranger;
    
    /**
     * @var int
     *
     * @ORM\Column(name="nombreEmployePrevisionnel", type="integer", nullable=true)
     */
    private $nombreEmployePrevisionnel;

    /** 	
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\DossierDemande")
     * @ORM\JoinColumn(name="idDossierDemande", referencedColumnName="id")
     */
    private $dossierDemande;
    
    /** 	
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\TypeStructure")
     * @ORM\JoinColumn(name="idTypeStructure", referencedColumnName="id")
     */
    private $typeStructure;
    
    /**
     * @var int
     *
     * @ORM\Column(name="numeroCNSS", type="string", nullable=true)
     */
    private $numeroCNSS;
    
     /**
     * @var int
     *
     * @ORM\Column(name="nombreEtablissement", type="integer", nullable=true)
     */
    private $nombreEtablissement;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nomSignataire", type="string", length=200,nullable=true)
     */
    private $nomSignataire;
     /**
     * @var string
     *
     * @ORM\Column(name="fonctionSignataire", type="string", length=200,nullable=true)
     */
    private $fonctionSignataire;

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
     * Set enActivite
     *
     * @param boolean $enActivite
     * @return Aguipe
     */
    public function setEnActivite($enActivite)
    {
        $this->enActivite = $enActivite;

        return $this;
    }

    /**
     * Get enActivite
     *
     * @return boolean 
     */
    public function getEnActivite()
    {
        return $this->enActivite;
    }

    /**
     * Set dateDebutActivite
     *
     * @param \DateTime $dateDebutActivite
     * @return Aguipe
     */
    public function setDateDebutActivite($dateDebutActivite)
    {
        $this->dateDebutActivite = $dateDebutActivite;

        return $this;
    }

    /**
     * Get dateDebutActivite
     *
     * @return \DateTime 
     */
    public function getDateDebutActivite()
    {
        return $this->dateDebutActivite;
    }

    /**
     * Set nombreEmployeGuineen
     *
     * @param integer $nombreEmployeGuineen
     * @return Aguipe
     */
    public function setNombreEmployeGuineen($nombreEmployeGuineen)
    {
        $this->nombreEmployeGuineen = $nombreEmployeGuineen;

        return $this;
    }

    /**
     * Get nombreEmployeGuineen
     *
     * @return integer 
     */
    public function getNombreEmployeGuineen()
    {
        return $this->nombreEmployeGuineen;
    }

    /**
     * Set nombreEmployeEtranger
     *
     * @param integer $nombreEmployeEtranger
     * @return Aguipe
     */
    public function setNombreEmployeEtranger($nombreEmployeEtranger)
    {
        $this->nombreEmployeEtranger = $nombreEmployeEtranger;

        return $this;
    }

    /**
     * Get nombreEmployeEtranger
     *
     * @return integer 
     */
    public function getNombreEmployeEtranger()
    {
        return $this->nombreEmployeEtranger;
    }

    /**
     * Set dossierDemande
     *
     * @param \BanquemondialeBundle\Entity\DossierDemande $dossierDemande
     * @return Aguipe
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
     * Set nombreEmployePrevisionnel
     *
     * @param integer $nombreEmployePrevisionnel
     * @return Aguipe
     */
    public function setNombreEmployePrevisionnel($nombreEmployePrevisionnel)
    {
        $this->nombreEmployePrevisionnel = $nombreEmployePrevisionnel;

        return $this;
    }

    /**
     * Get nombreEmployePrevisionnel
     *
     * @return integer 
     */
    public function getNombreEmployePrevisionnel()
    {
        return $this->nombreEmployePrevisionnel;
    }
    
    public function setNombreEmployeActuel($nombreEmployeActuel)
    {
        $this->nombreEmployeActuel = $nombreEmployeActuel;

        return $this;
    }
    
    public function getNombreEmployeActuel()
    {
        return $this->nombreEmployeActuel;
    }

    

    /**
     * Set typeStructure
     *
     * @param \BanquemondialeBundle\Entity\TypeStructure $typeStructure
     * @return Aguipe
     */
    public function setTypeStructure(\BanquemondialeBundle\Entity\TypeStructure $typeStructure = null)
    {
        $this->typeStructure = $typeStructure;

        return $this;
    }

    /**
     * Get typeStructure
     *
     * @return \BanquemondialeBundle\Entity\TypeStructure 
     */
    public function getTypeStructure()
    {
        return $this->typeStructure;
    }

    /**
     * Set numeroCNSS
     *
     * @param integer $numeroCNSS
     * @return Aguipe
     */
    public function setNumeroCNSS($numeroCNSS)
    {
        $this->numeroCNSS = $numeroCNSS;

        return $this;
    }

    /**
     * Get numeroCNSS
     *
     * @return integer 
     */
    public function getNumeroCNSS()
    {
        return $this->numeroCNSS;
    }

    /**
     * Set dateImmatriculation
     *
     * @param \DateTime $dateImmatriculation
     * @return Aguipe
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
     * Set nombreEtablissement
     *
     * @param integer $nombreEtablissement
     * @return Aguipe
     */
    public function setNombreEtablissement($nombreEtablissement)
    {
        $this->nombreEtablissement = $nombreEtablissement;

        return $this;
    }

    /**
     * Get nombreEtablissement
     *
     * @return integer 
     */
    public function getNombreEtablissement()
    {
        return $this->nombreEtablissement;
    }

    /**
     * Set nomSignataire
     *
     * @param string $nomSignataire
     * @return Aguipe
     */
    public function setNomSignataire($nomSignataire)
    {
        $this->nomSignataire = $nomSignataire;

        return $this;
    }

    /**
     * Get nomSignataire
     *
     * @return string 
     */
    public function getNomSignataire()
    {
        return $this->nomSignataire;
    }

    /**
     * Set fonctionSignataire
     *
     * @param string $fonctionSignataire
     * @return Aguipe
     */
    public function setFonctionSignataire($fonctionSignataire)
    {
        $this->fonctionSignataire = $fonctionSignataire;

        return $this;
    }

    /**
     * Get fonctionSignataire
     *
     * @return string 
     */
    public function getFonctionSignataire()
    {
        return $this->fonctionSignataire;
    }

    /**
     * Set numeroImmatriculation
     *
     * @param string $numeroImmatriculation
     * @return Aguipe
     */
    public function setNumeroImmatriculation($numeroImmatriculation)
    {
        $this->numeroImmatriculation = $numeroImmatriculation;

        return $this;
    }

    /**
     * Get numeroImmatriculation
     *
     * @return string 
     */
    public function getNumeroImmatriculation()
    {
        return $this->numeroImmatriculation;
    }
}
