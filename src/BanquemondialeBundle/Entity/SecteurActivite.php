<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SecteurActivite
 *
 * @ORM\Table(name="secteuractivite")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\SecteurActiviteRepository")
 */
class SecteurActivite
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
     * @ORM\Column(name="code", type="string", length=25)
     */
    private $code;
	

      /**
      * @ORM\OneToMany(targetEntity="ParametrageBundle\Entity\SecteurActiviteTraduction",mappedBy="secteurActivite", cascade={"persist","remove"})
      */ 
    private $secteurActiviteTraduction;
	
	
	/**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\CategorieActivite" )
      * @ORM\JoinColumn(name="idCategorieActivite", referencedColumnName="id")
      */ 
    private $categorieActivite;
	
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean", options={"default":true})
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
     * Set code
     *
     * @param string $code
     * @return SecteurActivite
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
     * Constructor
     */
    public function __construct()
    {
        $this->secteurActiviteTraduction = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add secteurActiviteTraduction
     *
     * @param \ParametrageBundle\Entity\SecteurActiviteTraduction $secteurActiviteTraduction
     * @return SecteurActivite
     */
    public function addSecteurActiviteTraduction(\ParametrageBundle\Entity\SecteurActiviteTraduction $secteurActiviteTraduction)
    {
        $this->secteurActiviteTraduction[] = $secteurActiviteTraduction;

        return $this;
    }

    /**
     * Remove secteurActiviteTraduction
     *
     * @param \ParametrageBundle\Entity\SecteurActiviteTraduction $secteurActiviteTraduction
     */
    public function removeSecteurActiviteTraduction(\ParametrageBundle\Entity\SecteurActiviteTraduction $secteurActiviteTraduction)
    {
        $this->secteurActiviteTraduction->removeElement($secteurActiviteTraduction);
    }

    /**
     * Get secteurActiviteTraduction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSecteurActiviteTraduction()
    {
        return $this->secteurActiviteTraduction;
    }
	
  /**
   * Get categorieActivite
   *
   * @return \BanquemondialeBundle\Entity\CategorieActivite
   */
    public function getCategorieActivite() {
        return $this->categorieActivite;
    }
	
    /**
     *  Set categorieActivite
     *
     * @param \BanquemondialeBundle\Entity\SecteurActivite $secteurActivite
     * @return CategorieActivite
     */
    public function setCategorieActivite(\BanquemondialeBundle\Entity\CategorieActivite $categorieActivite = null) {
        $this->categorieActivite = $categorieActivite;
		return $this;
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
    public function getActif()
    {
        return $this->actif;
    }
	
	
    public function __toString() {
        return $this->getCode();
    }
}
