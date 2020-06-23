<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prefecture
 *
 * @ORM\Table(name="prefecture")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\PrefectureRepository")
 */
class Prefecture
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
     * @ORM\Column(name="code", type="string", length=4)
     *  
     */
    private $code;
    /**
     * @var string
     *
     * @ORM\Column(name="Libelle", type="string", length=100, nullable=true)
     */
    private $libelle;

    /**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Region")
      */
    private $region;

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
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return Prefecture
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
     * Set region
     *
     * @param \BanquemondialeBundle\Entity\Region $region
     * @return Prefecture
     */
    public function setRegion(\BanquemondialeBundle\Entity\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \BanquemondialeBundle\Entity\Region 
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return Prefecture
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

    /**
     * Set code
     *
     * @param string $code
     * @return Prefecture
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
}
