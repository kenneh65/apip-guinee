<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LibelleTarification
 *
 * @ORM\Table(name="libelletarification")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\LibelleTarificationRepository")
 */
class LibelleTarification
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
     * @ORM\Column(name="libelle", type="string", length=100, unique=true)
     */
    private $libelle;
	
	/**
     * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\Pole")
     * @ORM\JoinColumn(name="idPole", referencedColumnName="id")
     */
    private $pole;


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
     * @return libelleTarification
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
     * Set pole
     *
     * @param \ParametrageBundle\Entity\Pole $pole
     * @return ComplementPole
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

}
