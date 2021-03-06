<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Circuit
 *
 * @ORM\Table(name="circuit")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\CircuitRepository")
 */
class Circuit
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
     * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\Pole")
     * @ORM\JoinColumn(name="idPole", referencedColumnName="id")
     */
    private $pole;
    
    /**
     * @var int     
     * @ORM\Column(name="ordre", type="integer")
     * @Assert\Range(min=0)
     */
    private $ordre;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\TypeDossier")
     * @ORM\JoinColumn(name="idTypeDossier", referencedColumnName="id")
     */
    private $typeDossier;

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
     * Set ordre
     *
     * @param integer $ordre
     * @return Circuit
     */
    
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer 
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set typeOperation
     *
     * @param \BanquemondialeBundle\Entity\TypeOperation $typeOperation
     * @return Circuit
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
     * @return Circuit
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
     * Set pole
     *
     * @param \ParametrageBundle\Entity\Pole $pole
     * @return Circuit
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
     * Set typeDossier
     *
     * @param \BanquemondialeBundle\Entity\TypeDossier $typeDossier
     * @return Circuit
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
}
