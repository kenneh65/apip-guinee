<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fonctionnalite
 *
 * @ORM\Table(name="fonctionnalite")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\FonctionnaliteRepository")
 */
class Fonctionnalite {

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
     * @ORM\Column(name="titre", type="string", length=100, unique=true)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=50)
     */
    private $route;

    /**
     * @var string
     *
     * @ORM\Column(name="routePole", type="string", length=50,nullable=true)
     */
    private $routePole;

    /**
     * @ORM\OneToMany(targetEntity="ParametrageBundle\Entity\FonctionnaliteTraduction", mappedBy="fonctionnalite")
     */
    private $fonctionnaliteTraductions;

    /**
     * @var string
     *
     * @ORM\Column(name="nomPole", type="string", length=50,nullable=true)
     */
    private $pole;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set route
     *
     * @param string $route
     * @return Fonctionnalite
     */
    public function setRoute($route) {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Fonctionnalite
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
     * Set routePole
     *
     * @param string $routePole
     * @return Fonctionnalite
     */
    public function setRoutePole($routePole) {
        $this->routePole = $routePole;

        return $this;
    }

    /**
     * Get routePole
     *
     * @return string 
     */
    public function getRoutePole() {
        return $this->routePole;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->fonctionnaliteTraductions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add fonctionnaliteTraductions
     *
     * @param \ParametrageBundle\Entity\FonctionnaliteTraduction $fonctionnaliteTraductions
     * @return Fonctionnalite
     */
    public function addFonctionnaliteTraduction(\ParametrageBundle\Entity\FonctionnaliteTraduction $fonctionnaliteTraduction) {
        $this->fonctionnaliteTraductions[] = $fonctionnaliteTraduction;
        $fonctionnaliteTraduction->setFonctionnalite($this);
        return $this;
    }

    /**
     * Remove fonctionnaliteTraductions
     *
     * @param \ParametrageBundle\Entity\FonctionnaliteTraduction $fonctionnaliteTraductions
     */
    public function removeFonctionnaliteTraduction(\ParametrageBundle\Entity\FonctionnaliteTraduction $fonctionnaliteTraduction) {
        $this->fonctionnaliteTraductions->removeElement($fonctionnaliteTraduction);
    }

    /**
     * Get fonctionnaliteTraductions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFonctionnaliteTraductions() {
        return $this->fonctionnaliteTraductions;
    }

    /**
     * Set pole
     *
     * @param string $pole
     * @return Fonctionnalite
     */
    public function setPole($pole)
    {
        $this->pole = $pole;

        return $this;
    }

    /**
     * Get pole
     *
     * @return string 
     */
    public function getPole()
    {
        return $this->pole;
    }
}
