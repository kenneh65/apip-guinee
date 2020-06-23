<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModePaiementTraduction
 *
 * @ORM\Table(name="ModePaiementtraduction")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\ModePaiementTraductionRepository")
 */
class ModePaiementTraduction
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
     * @ORM\Column(name="libelle", type="string", length=80)
     */
    private $libelle;
	
	
	/**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\ModePaiement", cascade={"persist","remove"})
      * @ORM\JoinColumn(name="idModePaiement", referencedColumnName="id")
      */
    private $modePaiement;
	
	
	/**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Langue", cascade={"persist","remove"})
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
     * @return ModePaiementTraduction
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
     * Set ModePaiement
     *
     * @param integer $ModePaiement
     * @return ModePaiementTraduction
     */
    public function setModePaiement($modePaiement)
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    /**
     * Get ModePaiement
     *
     * @return integer 
     */
    public function getModePaiement()
    {
        return $this->modePaiement;
    }

    /**
     * Set langue
     *
     * @param integer $langue
     * @return ModePaiementTraduction
     */
    public function setLangue($langue)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return integer 
     */
    public function getLangue()
    {
        return $this->langue;
    }
	
	public function __toString()
	{
		return (String)$this->getLibelle();
	}
}
