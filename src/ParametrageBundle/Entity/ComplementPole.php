<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ComplementPole
 *
 * @ORM\Table(name="complementpole")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\ComplementPoleRepository")
 */
class ComplementPole
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
     * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\TypeDonnee")
     * @ORM\JoinColumn(name="idTypeData", referencedColumnName="id")
     */
    private $typedonnee;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100)
     */
    private $libelle;
    
	/**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean", options={"default":true})
     */
    private $actif;
	
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="requis", type="boolean", options={"default":true})
     */
    private $requis;

	
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
     * @return ComplementPole
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
     * Set typedonnee
     *
     * @param \ParametrageBundle\Entity\TypeDonnee $typedonnee
     * @return ComplementPole
     */
    public function setTypedonnee(\ParametrageBundle\Entity\TypeDonnee $typedonnee = null)
    {
        $this->typedonnee = $typedonnee;

        return $this;
    }

    /**
     * Get typedonnee
     *
     * @return \ParametrageBundle\Entity\TypeDonnee 
     */
    public function getTypedonnee()
    {
        return $this->typedonnee;
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

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return ComplementPole
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
     * Set requis
     *
     * @param boolean $requis
     * @return ComplementPole
     */
    public function setRequis($requis)
    {
        $this->requis = $requis;

        return $this;
    }

    /**
     * Get requis
     *
     * @return boolean 
     */
    public function getRequis()
    {
        return $this->requis;
    }
}
