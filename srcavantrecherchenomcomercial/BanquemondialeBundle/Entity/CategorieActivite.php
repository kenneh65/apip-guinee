<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategorieActivite
 *
 * @ORM\Table(name="categorieactivite")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\CategorieActiviteRepository")
 */
class CategorieActivite
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
      * @ORM\OneToMany(targetEntity="ParametrageBundle\Entity\CategorieActiviteTraduction",mappedBy="categorieActivite", cascade={"persist","remove"})
      */ 
    private $categorieActiviteTraduction;
	
	/**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=25)
     */
    private $code;

    /**
     * @var bool
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;


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
     * Constructor
     */
    public function __construct()
    {
        $this->secteurActiviteTraduction = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add categorieActiviteTraduction
     *
     * @param \ParametrageBundle\Entity\CategorieActiviteTraduction $categorieActiviteTraduction
     * @return CategorieActivite
     */
    public function addCategorieActiviteTraduction(\ParametrageBundle\Entity\CategorieActiviteTraduction $categorieActiviteTraduction)
    {
        $this->categorieActiviteTraduction[] = $categorieActiviteTraduction;

        return $this;
    }

    /**
     * Remove categorieActiviteTraduction
     *
     * @param \ParametrageBundle\Entity\CategorieActiviteTraduction $categorieActiviteTraduction
     */
    public function removeCategorieActiviteTraduction(\ParametrageBundle\Entity\CategorieActiviteTraduction $categorieActiviteTraduction)
    {
        $this->categorieActiviteTraduction->removeElement($categorieActiviteTraduction);
    }

    /**
     * Get categorieActiviteTraduction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategorieActiviteTraduction()
    {
        return $this->categorieActiviteTraduction;
    }
	
	
    /**
     * Set code
     *
     * @param string $code
     * @return CategorieActivite
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

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return CategorieActivite
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
}
