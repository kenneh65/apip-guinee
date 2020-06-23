<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fonction
 *
 * @ORM\Table(name="fonction")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\FonctionRepository")
 */
class Fonction {

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
     * @ORM\Column(name="code", type="string", length=5)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="BanquemondialeBundle\Entity\FonctionTraduction",mappedBy="fonction", cascade={"persist","remove"})
     */
    private $fonctionTraduction;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean", options={"default":true})
     */
    private $actif=true;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Fonction
     */
    public function setCode($code) {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode() {
        return $this->code;
    }
	

    /**
     * Set description
     *
     * @param string $description
     * @return Fonction
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Constructor
     */
    public function __construct() {
		$this->actif = true;
        $this->fonctionTraduction = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add fonctionTraduction
     *
     * @param \BanquemondialeBundle\Entity\FonctionTraduction $fonctionTraduction
     * @return Fonction
     */
    public function addFonctionTraduction(\BanquemondialeBundle\Entity\FonctionTraduction $fonctionTraduction) {
        $this->fonctionTraduction[] = $fonctionTraduction;

        return $this;
    }

    /**
     * Remove fonctionTraduction
     *
     * @param \BanquemondialeBundle\Entity\FonctionTraduction $fonctionTraduction
     */
    public function removeFonctionTraduction(\BanquemondialeBundle\Entity\FonctionTraduction $fonctionTraduction) {
        $this->fonctionTraduction->removeElement($fonctionTraduction);
    }

    /**
     * Get fonctionTraduction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFonctionTraduction() {
        return $this->fonctionTraduction;
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
	
	

    public function __toString() {
        return (String) $this->getId();
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
