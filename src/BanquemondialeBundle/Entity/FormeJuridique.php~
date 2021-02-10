<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FormeJuridique
 *
 * @ORM\Table(name="formejuridique")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\FormeJuridiqueRepository")
 */
class FormeJuridique
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
     * @ORM\Column(name="sigle", type="string", length=100)
     */
    private $sigle;
	
	

	/**
      * @ORM\OneToMany(targetEntity="BanquemondialeBundle\Entity\FormeJuridiqueTraduction",mappedBy="formeJuridique", cascade={"persist","remove"})
      */ 
    private $formeJuridiqueTraduction;
	
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean", nullable=true, options={"default":true})
     */
    private $actif=true;
	
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
     * @return formeJuridique
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

    public function __toString() {
        return $this->getSigle();
    }

    /**
     * Set sigle
     *
     * @param string $sigle
     * @return FormeJuridique
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
     * Constructor
     */
    public function __construct()
    {
        $this->formeJuridiqueTraduction = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add formeJuridiqueTraduction
     *
     * @param \BanquemondialeBundle\Entity\FormeJuridiqueTraduction $formeJuridiqueTraduction
     * @return FormeJuridique
     */
    public function addFormeJuridiqueTraduction(\BanquemondialeBundle\Entity\FormeJuridiqueTraduction $formeJuridiqueTraduction)
    {
        $this->formeJuridiqueTraduction[] = $formeJuridiqueTraduction;

        return $this;
    }

    /**
     * Remove formeJuridiqueTraduction
     *
     * @param \BanquemondialeBundle\Entity\FormeJuridiqueTraduction $formeJuridiqueTraduction
     */
    public function removeFormeJuridiqueTraduction(\BanquemondialeBundle\Entity\FormeJuridiqueTraduction $formeJuridiqueTraduction)
    {
        $this->formeJuridiqueTraduction->removeElement($formeJuridiqueTraduction);
    }

    /**
     * Get formeJuridiqueTraduction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFormeJuridiqueTraduction()
    {
        return $this->formeJuridiqueTraduction;
    }


	/**
     * Set actif
     *
     * @param boolean $actif
     * @return SecteurActivite
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean 
     */
    public function isActif()
    {
        return $this->actif;
    }


    /**
     * Get actif
     *
     * @return boolean 
     */
    public function getActif()
    {
        return $this->actif;
    }
}
