<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FormeJuridiqueTraduction
 *
 * @ORM\Table(name="formeJuridiqueTraduction")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository")
 */
class FormeJuridiqueTraduction
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
     * @var string
     *
     * @ORM\Column(name="sigle", type="string", length=100)
     */
    private $sigle;

	
     /**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\FormeJuridique", inversedBy="formeJuridiqueTraduction")
      * @ORM\JoinColumn(name="idformeJuridique", referencedColumnName="id")
      */ 
    private $formeJuridique;
   
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
     * @return FormeJuridiqueTraduction
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
     * Get libelle
     *
     * @return string 
     */
    public function getFullLibelle()
    {
        return "(". $this->sigle. ")  - ".$this->libelle;
    }
	

    /**
     * Set formeJuridique
     *
     * @param \BanquemondialeBundle\Entity\FormeJuridique $formeJuridique
     * @return FormeJuridiqueTraduction
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
     * Set langue
     *
     * @param \BanquemondialeBundle\Entity\Langue $langue
     * @return FormeJuridiqueTraduction
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
     * Set sigle
     *
     * @param string $sigle
     * @return FormeJuridiqueTraduction
     */
    public function setSigle($sigle)
    {
        $this->sigle = $sigle;

        return $this;
    }

    /**
     * Get sigle
     *
     * @return string 
     */
    public function getSigle()
    {
        return $this->sigle;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return FormeJuridiqueTraduction
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    public function __toString() {
        return $this->libelle;
    }
}
