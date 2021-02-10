<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Liensutiles
 *
 * @ORM\Table(name="liensutiles")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\LiensutilesRepository")
 */
class Liensutiles {

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
     * @ORM\Column(name="titre", type="string", length=100)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=100)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1000)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="LiensutilesTraduction",mappedBy="lien",cascade={"remove","persist"})
     */
    private $traduction;

    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Liensutiles
     */
    public function setTitre($titre) {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre() {
        return $this->titre;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Liensutiles
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
     * Set image
     *
     * @param \BanquemondialeBundle\Entity\Media $image
     * @return Liensutiles
     */
    public function setImage(\BanquemondialeBundle\Entity\Media $image) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \BanquemondialeBundle\Entity\Media 
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Liensutiles
     */
    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->traduction = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add traduction
     *
     * @param \BanquemondialeBundle\Entity\LiensutilesTraduction $traduction
     * @return Liensutiles
     */
    public function addTraduction(\BanquemondialeBundle\Entity\LiensutilesTraduction $traduction) {
        $this->traduction[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \BanquemondialeBundle\Entity\LiensutilesTraduction $traduction
     */
    public function removeTraduction(\BanquemondialeBundle\Entity\LiensutilesTraduction $traduction) {
        $this->traduction->removeElement($traduction);
    }

    /**
     * Get traduction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTraduction() {
        return $this->traduction;
    }


    
}
