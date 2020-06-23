<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeDossier
 *
 * @ORM\Table(name="typedossier")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\TypeDossierRepository")
 */
class TypeDossier
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
     * @ORM\Column(name="libelle", type="string", length=60, unique=true)
     */
    private $libelle;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="attestationPayement", type="boolean", nullable=true, options={"default":false})
     */
    private $attestationPayement=true;
    
	/**
     * @var boolean
     *
     * @ORM\Column(name="enableByDEPOT", type="boolean", nullable=true, options={"default":true})
     */
    private $enableByDEPOT=true;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="enableBySaisi", type="boolean", nullable=true, options={"default":true})
     */
    private $enableBySaisi=true;
    
	
    
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
     * @return TypeDossier
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
     * Set attestationPayement
     *
     * @param boolean $attestationPayement
     * @return TypeDossier
     */
    public function setAttestationPayement($attestationPayement)
    {
        $this->attestationPayement = $attestationPayement;

        return $this;
    }

    /**
     * Get attestationPayement
     *
     * @return boolean 
     */
    public function getAttestationPayement()
    {
        return $this->attestationPayement;
    }

    

    /**
     * Set pole
     *
     * @param \ParametrageBundle\Entity\Pole $pole
     * @return TypeDossier
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
     * Set enableByDEPOT
     *
     * @param boolean $enableByDEPOT
     * @return TypeDossier
     */
    public function setEnableByDEPOT($enableByDEPOT)
    {
        $this->enableByDEPOT = $enableByDEPOT;

        return $this;
    }

    /**
     * Get enableByDEPOT
     *
     * @return boolean 
     */
    public function getEnableByDEPOT()
    {
        return $this->enableByDEPOT;
    }
	
	
	/**
     * Set enableBySaisi
     *
     * @param boolean $enableBySaisi
     * @return TypeDossier
     */
    public function setEnableBySaisi($enableBySaisi)
    {
        $this->enableBySaisi = $enableBySaisi;

        return $this;
    }

    /**
     * Get enableBySaisi
     *
     * @return boolean 
     */
    public function getEnableBySaisi()
    {
        return $this->enableBySaisi;
    }
}
