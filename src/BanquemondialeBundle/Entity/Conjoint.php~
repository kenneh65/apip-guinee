<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Conjoint
 *
 * @ORM\Table(name="conjoint")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\ConjointRepository")
 */
class Conjoint
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
     * @Assert\Length(max=80)
	 * @Assert\NotBlank()
     * @ORM\Column(name="nom", type="string", length=80)
     */
    private $nom;

    /**
     * @var string
     * @Assert\Length(max=80)
	 * @Assert\NotBlank()
     * @ORM\Column(name="prenom", type="string", length=80)
     */
    private $prenom;


    /**
     * @var string
     * @Assert\Length(max=150)
     * @ORM\Column(name="lieuMariage", type="string", length=150, nullable=true)
     */
    private $lieuMariage;
	
	
	/**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Representant",inversedBy="conjoints")
      * @ORM\JoinColumn(name="idRepresentant", referencedColumnName="id", onDelete="CASCADE")
      */
	private $representant;
	
	
	/**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(name="dateMariage", type="date", nullable=true)
     */
    private $dateMariage;
	
	
	/**
     * @var string
     * @Assert\Length(max=250)
     * @ORM\Column(name="optionMatrimoniale", type="string", length=250, nullable=true)
     */
    private $optionMatrimoniale;
	
	/**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\RegimeMatrimonial")
      * @ORM\JoinColumn(name="idRegimeMatrimonial", referencedColumnName="id")
      */
    private $regimeMatrimonial;
	
	
	/**
     * @var string
     * @Assert\Length(max=250)
     * @ORM\Column(name="clausesRestrictives", type="string", length=250, nullable=true)
     */
    private $clausesRestrictives;
	
	/**
     * @var string
     * @Assert\Length(max=250)
     * @ORM\Column(name="demandeSeparationBiens", type="string", length=250, nullable=true)
     */
    private $demandeSeparationBiens;
	

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
     * Set nom
     *
     * @param string $nom
     * @return Conjoint
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return Conjoint
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }


	
	
	/**
     * Set dateMariage
     *
     * @param \DateTime $dateMariage
     * @return Conjoint
     */
    public function setDateMariage($dateMariage)
    {
        $this->dateMariage = $dateMariage;

        return $this;
    }

    /**
     * Get dateMariage
     *
     * @return \DateTime 
     */
    public function getDateMariage()
    {
        return $this->dateMariage;
    }
	
	
    /**
     * Set lieuMariage
     *
     * @param string $lieuMariage
     * @return Conjoint
     */
    public function setLieuMariage($lieuMariage)
    {
        $this->lieuMariage = $lieuMariage;

        return $this;
    }

    /**
     * Get lieuMariage
     *
     * @return string 
     */
    public function getLieuMariage()
    {
        return $this->lieuMariage;
    }

    /**
     * Set representant
     *
     * @param \BanquemondialeBundle\Entity\Representant $representant
     * @return Conjoint
     */
    public function setRepresentant(\BanquemondialeBundle\Entity\Representant $representant = null)
    {
        $this->representant = $representant;

        return $this;
    }

    /**
     * Get representant
     *
     * @return \BanquemondialeBundle\Entity\Representant 
     */
    public function getRepresentant()
    {
        return $this->representant;
    }
	
	
	 /**
     * Set regimeMatrimonial
     *
     * @param \BanquemondialeBundle\Entity\RegimeMatrimonial $regimeMatrimonial
     * @return Conjoint
     */
    public function setRegimeMatrimonial(\BanquemondialeBundle\Entity\RegimeMatrimonial $regimeMatrimonial = null)
    {
        $this->regimeMatrimonial = $regimeMatrimonial;

        return $this;
    }

    /**
     * Get regimeMatrimonial
     *
     * @return \BanquemondialeBundle\Entity\RegimeMatrimonial 
     */
    public function getRegimeMatrimonial()
    {
        return $this->regimeMatrimonial;
    }
	
	
	  /**
     * Set optionMatrimoniale
     *
     * @param string $optionMatrimoniale
     * @return Conjoint
     */
    public function setOptionMatrimoniale($optionMatrimoniale)
    {
        $this->optionMatrimoniale = $optionMatrimoniale;

        return $this;
    }

    /**
     * Get optionMatrimoniale
     *
     * @return string 
     */
    public function getOptionMatrimoniale()
    {
        return $this->optionMatrimoniale;
    }

	
	
	/**
     * Set clausesRestrictives
     *
     * @param string $clausesRestrictives
     * @return Conjoint
     */
    public function setClausesRestrictives($clausesRestrictives)
    {
        $this->clausesRestrictives = $clausesRestrictives;

        return $this;
    }

    /**
     * Get clausesRestrictives
     *
     * @return string 
     */
    public function getClausesRestrictives()
    {
        return $this->clausesRestrictives;
    }
	
	
	/**
     * Set demandeSeparationBiens
     *
     * @param string $demandeSeparationBiens
     * @return Conjoint
     */
    public function setDemandeSeparationBiens($demandeSeparationBiens)
    {
        $this->demandeSeparationBiens = $demandeSeparationBiens;

        return $this;
    }

    /**
     * Get demandeSeparationBiens
     *
     * @return string 
     */
    public function getDemandeSeparationBiens()
    {
        return $this->demandeSeparationBiens;
    }

}
