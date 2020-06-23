<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeEntrepriseTraduction
 *
 * @ORM\Table(name="typeentreprisetraduction")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\TypeEntrepriseTraductionRepository")
 */
class TypeEntrepriseTraduction
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
      * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\TypeEntreprise", cascade={"persist","remove"})
      * @ORM\JoinColumn(name="idTypeEntreprise", referencedColumnName="id")
      */
    private $typeEntreprise;
	
	
	/**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Langue", cascade={"persist","remove"})
      * @ORM\JoinColumn(name="idLangue", referencedColumnName="id")
      */
    private $langue;


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
     * @return TypeEntrepriseTraduction
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
     * Set typeEntreprise
     *
     * @param integer $typeEntreprise
     * @return TypeEntrepriseTraduction
     */
    public function setGenre($typeEntreprise)
    {
        $this->typeEntreprise = $typeEntreprise;

        return $this;
    }

    /**
     * Get typeEntreprise
     *
     * @return integer 
     */
    public function getTypeEntreprise()
    {
        return $this->typeEntreprise;
    }

    /**
     * Set langue
     *
     * @param integer $langue
     * @return TypeEntrepriseTraduction
     */
    public function setLangue($langue)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return integer 
     */
    public function getLangue()
    {
        return $this->langue;
    }
	
	public function __toString()
	{
		return (String)$this->getLibelle();
	}
}
