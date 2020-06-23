<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tarification
 *
 * @ORM\Table(name="tarification")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\TarificationRepository")
 */
class Tarification
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
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\TypeOperation")
     * @ORM\JoinColumn(name="idTypeOperation", referencedColumnName="id")
     */
    private $typeOperation;
	
	
    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\FormeJuridique")
     * @ORM\JoinColumn(name="idFormeJuridique", referencedColumnName="id")
     */
    private $formeJuridique;
    
    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float", nullable=true)
     */
    private $montant;
	
	/**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\TypeDossier")
     * @ORM\JoinColumn(name="idTypeDossier", referencedColumnName="id")
     */
    private $typeDossier;
	
	
	/**
     * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\LibelleTarification")
     * @ORM\JoinColumn(name="idLibelleTarification", referencedColumnName="id")
     */
    private $libelleTarification;


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
     * Set montant
     *
     * @param float $montant
     * @return Tarification
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return float 
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set pole
     *
     * @param \ParametrageBundle\Entity\Pole $pole
     * @return Tarification
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
     * Set typeOperation
     *
     * @param \BanquemondialeBundle\Entity\TypeOperation $typeOperation
     * @return Tarification
     */
    public function setTypeOperation(\BanquemondialeBundle\Entity\TypeOperation $typeOperation = null)
    {
        $this->typeOperation = $typeOperation;

        return $this;
    }

    /**
     * Get typeOperation
     *
     * @return \BanquemondialeBundle\Entity\TypeOperation 
     */
    public function getTypeOperation()
    {
        return $this->typeOperation;
    }

    /**
     * Set formeJuridique
     *
     * @param \BanquemondialeBundle\Entity\FormeJuridique $formeJuridique
     * @return Tarification
     */
    public function setFormeJuridique(\BanquemondialeBundle\Entity\FormeJuridique $formeJuridique = null)
    {
        $this->formeJuridique = $formeJuridique;

        return $this;
    }

    /**
     * Get formeJuridique
     *
     * @return \BanquemondialeBundle\Entity\FormeJuridique 
     */
    public function getFormeJuridique()
    {
        return $this->formeJuridique;
    }
	
	
	/**
     * Set typeDossier
     *
     * @param \BanquemondialeBundle\Entity\TypeDossier $typeDossier
     * @return Tarification
     */
    public function setTypeDossier(\BanquemondialeBundle\Entity\TypeDossier $typeDossier = null)
    {
        $this->typeDossier = $typeDossier;

        return $this;
    }

    /**
     * Get typeDossier
     *
     * @return \BanquemondialeBundle\Entity\TypeDossier 
     */
    public function getTypeDossier()
    {
        return $this->typeDossier;
    }
	
	
	/**
     * Set libelleTarification
     *
     * @param \BanquemondialeBundle\Entity\LibelleTarification $libelleTarification
     * @return Tarification
     */
    public function setLibelleTarification(\ParametrageBundle\Entity\LibelleTarification $libelleTarification = null)
    {
        $this->libelleTarification = $libelleTarification;

        return $this;
    }

    /**
     * Get libelleTarification
     *
     * @return \ParametrageBundle\Entity\LibelleTarification 
     */
    public function getLibelleTarification()
    {
        return $this->libelleTarification;
    }
}
