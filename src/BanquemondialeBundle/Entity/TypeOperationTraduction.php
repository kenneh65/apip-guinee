<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeOperationTraduction
 *
 * @ORM\Table(name="typeoperationtraduction")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\TypeOperationTraductionRepository")
 */
class TypeOperationTraduction
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
     * @ORM\Column(name="libelle", type="string", length=100)
     */
    private $libelle;

    /**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\TypeOperation")
      * @ORM\JoinColumn(name="idTypeOperation", referencedColumnName="id")
      */
    private $typeOperation;
/**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Langue")
      * @ORM\JoinColumn(name="idLangue", referencedColumnName="id")
      */
    private $langue;
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
     * Set libelle
     *
     * @param string $libelle
     * @return TypeOperationTraduction
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }
    

    /**
     * Set langue
     *
     * @param \BanquemondialeBundle\Entity\Langue $langue
     * @return TypeOperationTraduction
     */
    public function setLangue(\BanquemondialeBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return \BanquemondialeBundle\Entity\Langue 
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set typeOperation
     *
     * @param \BanquemondialeBundle\Entity\TypeOperation $typeOperation
     * @return TypeOperationTraduction
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
    
    public function __toString()
	{
		return (String)$this->getLibelle();
	}
}
