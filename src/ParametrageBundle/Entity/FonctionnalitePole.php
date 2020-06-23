<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FonctinnalitePole
 *
 * @ORM\Table(name="fonctionnalitepole")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\FonctionnalitePoleRepository")
 */
class FonctionnalitePole
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
     * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\Pole")
     * @ORM\JoinColumn(name="idPole", referencedColumnName="id")
     */
    private $pole;
    
    /**
     * @var string
     *
     * @ORM\Column(name="codeFormeJuridique", type="string", length=4,nullable=true)
     */
    private $codeFormeJuridique;
    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=50, unique=true)
     */
    private $route;


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
     * Set route
     *
     * @param string $route
     * @return FonctinnalitePole
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set pole
     *
     * @param \ParametrageBundle\Entity\Pole $pole
     * @return FonctionnalitePole
     */
    public function setPole(\ParametrageBundle\Entity\Pole $pole = null)
    {
        $this->pole = $pole;

        return $this;
    }

    /**
     * Get pole
     *
     * @return \ParametrageBundle\Entity\Pole 
     */
    public function getPole()
    {
        return $this->pole;
    }

    /**
     * Set codeFormeJuridique
     *
     * @param string $codeFormeJuridique
     * @return FonctionnalitePole
     */
    public function setCodeFormeJuridique($codeFormeJuridique)
    {
        $this->codeFormeJuridique = $codeFormeJuridique;

        return $this;
    }

    /**
     * Get codeFormeJuridique
     *
     * @return string 
     */
    public function getCodeFormeJuridique()
    {
        return $this->codeFormeJuridique;
    }
}
