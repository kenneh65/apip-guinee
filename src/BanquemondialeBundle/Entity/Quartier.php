<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Quartier
 *
 * @ORM\Table(name="quartier")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\QuartierRepository")
 */
class Quartier {

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
     * @ORM\Column(name="Libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="Code", type="string", length=8, nullable=true)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\SousPrefectureCommune")
     * @ORM\JoinColumn(name="idSousPrefecture", referencedColumnName="id")
     */
    private $sousPrefecture;
    
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
     * Set libelle
     *
     * @param string $libelle
     * @return Quartier
     */
    public function setLibelle($libelle) {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle() {
        return $this->libelle;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Quartier
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
     * Set sousPrefecture
     *
     * @param \BanquemondialeBundle\Entity\SousPrefectureCommune $sousPrefecture
     * @return Quartier
     */
    public function setSousPrefecture(\BanquemondialeBundle\Entity\SousPrefectureCommune $sousPrefecture = null)
    {
        $this->sousPrefecture = $sousPrefecture;

        return $this;
    }

    /**
     * Get sousPrefecture
     *
     * @return \BanquemondialeBundle\Entity\SousPrefectureCommune 
     */
    public function getSousPrefecture()
    {
        return $this->sousPrefecture;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return Quartier
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
}
