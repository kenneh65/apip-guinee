<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pays
 *
 * @ORM\Table(name="pays")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\PaysRepository")
 */
class Pays
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
     * @ORM\Column(name="code", type="string", length=2)
	 * @Assert\Length(min = 2,max = 2)
     */
    private $code;		

    /**
     * @var boolean
     * @ORM\Column(name="residence",type="boolean", options={"default":false})
     */
    private $residence;
	
	/**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Continent")
     * @ORM\JoinColumn(name="idContinent", referencedColumnName="id")
     */
    private $continent;

    /**
      * @ORM\OneToMany(targetEntity="BanquemondialeBundle\Entity\Region",mappedBy="pays", cascade={"persist"})
      */ 
    private $regions;

    /**
      * @ORM\OneToMany(targetEntity="BanquemondialeBundle\Entity\PaysTraduction",mappedBy="pays", cascade={"persist","remove"})
      */ 
    private $paysTraduction;
	
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
     * Set code
     *
     * @param string $code
     * @return Pays
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
	
	public function __toString()
	{
		return (String)$this->getCode();
	}
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->regions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->paysTraduction = new \Doctrine\Common\Collections\ArrayCollection();
        $this->residence = false;
    }

    /**
     * Add regions
     *
     * @param \BanquemondialeBundle\Entity\Region $regions
     * @return Pays
     */
    public function addRegion(\BanquemondialeBundle\Entity\Region $regions)
    {
        $this->regions[] = $regions;

        return $this;
    }

    /**
     * Remove regions
     *
     * @param \BanquemondialeBundle\Entity\Region $regions
     */
    public function removeRegion(\BanquemondialeBundle\Entity\Region $regions)
    {
        $this->regions->removeElement($regions);
    }

    /**
     * Get regions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * Add paysTraduction
     *
     * @param \BanquemondialeBundle\Entity\PaysTraduction $paysTraduction
     * @return Pays
     */
    public function addPaysTraduction(\BanquemondialeBundle\Entity\PaysTraduction $paysTraduction)
    {
        $this->paysTraduction[] = $paysTraduction;

        return $this;
    }

    /**
     * Remove paysTraduction
     *
     * @param \BanquemondialeBundle\Entity\PaysTraduction $paysTraduction
     */
    public function removePaysTraduction(\BanquemondialeBundle\Entity\PaysTraduction $paysTraduction)
    {
        $this->paysTraduction->removeElement($paysTraduction);
    }

    /**
     * Get paysTraduction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPaysTraduction()
    {
        return $this->paysTraduction;
    }

    /**
     * Set residence
     *
     * @param boolean $residence
     * @return Pays
     */
    public function setResidence($residence)
    {
        $this->residence = $residence;

        return $this;
    }

    /**
     * Get residence
     *
     * @return boolean 
     */
    public function getResidence()
    {
        return $this->residence;
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
     * Get continent
     *
     * @return \BanquemondialeBundle\Entity\Continent 
     */
    public function getContinent()
    {
        return $this->continent;
    } 
	
	 /**
     * Set continent
     *
     * @param \BanquemondialeBundle\Entity\Continent $continent
     * @return DossierDemande
     */
    public function setContinent(\BanquemondialeBundle\Entity\Continent $continent = null)
    {
        $this->continent = $continent;

        return $this;
    }  
}
