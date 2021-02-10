<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SousPrefectureCommune
 *
 * @ORM\Table(name="sousprefecturecommune")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\SousPrefectureCommuneRepository")
 */
class SousPrefectureCommune
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
     * @ORM\Column(name="Libelle", type="string", length=100)
     */
    private $libelle;

     /**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Prefecture")
      */
    private $prefecture;

    /**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\TypeLocalite")
      */
    private $typeLocalite;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean", options={"default":true})
     */
    private $actif;
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=6)
     *  
     */
    private $code;
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
     * @return SousPrefectureComune
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
     * Set prefecture
     *
     * @param \BanquemondialeBundle\Entity\Prefecture $prefecture
     * @return SousPrefectureCommune
     */
    public function setPrefecture(\BanquemondialeBundle\Entity\Prefecture $prefecture = null)
    {
        $this->prefecture = $prefecture;

        return $this;
    }

    /**
     * Get prefecture
     *
     * @return \BanquemondialeBundle\Entity\Prefecture 
     */
    public function getPrefecture()
    {
        return $this->prefecture;
    }

    /**
     * Set typeLocalite
     *
     * @param \BanquemondialeBundle\Entity\TypeLocalite $typeLocalite
     * @return SousPrefectureCommune
     */
    public function setTypeLocalite(\BanquemondialeBundle\Entity\TypeLocalite $typeLocalite = null)
    {
        $this->typeLocalite = $typeLocalite;

        return $this;
    }

    /**
     * Get typeLocalite
     *
     * @return \BanquemondialeBundle\Entity\TypeLocalite 
     */
    public function getTypeLocalite()
    {
        return $this->typeLocalite;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return SousPrefectureCommune
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
     * @return SousPrefectureCommune
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
