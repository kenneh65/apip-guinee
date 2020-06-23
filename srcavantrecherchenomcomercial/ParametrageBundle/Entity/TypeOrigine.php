<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeOrigine
 *
 * @ORM\Table(name="typeorigine")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\TypeOrigineRepository")
 */
class TypeOrigine
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
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;
	
	/**
     * @var bool
     *
     * @ORM\Column(name="SiPersonnePhysique", type="boolean", nullable=false)
     */
    private $siPersonnePhysique;


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
     * @return TypeOrigine
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
     * Set siPersonnePhysique
     *
     * @param boolean $siPersonnePhysique
     * @return TypeOrigine
     */
    public function setSiPersonnePhysique($siPersonnePhysique)
    {
        $this->siPersonnePhysique = $siPersonnePhysique;

        return $this;
    }

    /**
     * Get siPersonnePhysique
     *
     * @return boolean 
     */
    public function getSiPersonnePhysique()
    {
        return $this->siPersonnePhysique;
    }
}
