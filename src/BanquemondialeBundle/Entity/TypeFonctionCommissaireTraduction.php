<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeFonctionCommissaireTraduction
 *
 * @ORM\Table(name="typefonctioncommissairetraduction")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\TypeFonctionCommissaireTraductionRepository")
 */
class TypeFonctionCommissaireTraduction
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
     * @ORM\Column(name="libelle", type="string", length=80)
     */
    private $libelle;
	
	/**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\TypeFonctionCommissaire", inversedBy= "typeFonctionCommissaireTraduction")
      * @ORM\JoinColumn(name="idTypeFonctionCommissaire", referencedColumnName="id")
      */ 
    private $typeFonctionCommissaire;

    /**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Langue")
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
     * @return TypeFonctionCommissaireTraduction
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
     * Set langue
     *
     * @param \BanquemondialeBundle\Entity\Langue $langue
     * @return TypeFonctionCommissaireTraduction
     */
    public function setLangue(\BanquemondialeBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return \BanquemondialeBundle\Entity\Langue 
     */
    public function getLangue()
    {
        return $this->langue;
    }
	
	
	/**
     * Set typeFonctionCommissaire
     *
     * @param \BanquemondialeBundle\Entity\TypeFonctionCommissaire $typeFonctionCommissaire
     * @return TypeFonctionCommissaireTraduction
     */
    public function setTypeFonctionCommissaire(\BanquemondialeBundle\Entity\TypeFonctionCommissaire $typeFonctionCommissaire = null)
    {
        $this->typeFonctionCommissaire = $typeFonctionCommissaire;

        return $this;
    }

    /**
     * Get typeFonctionCommissaire
     *
     * @return \BanquemondialeBundle\Entity\TypeFonctionCommissaire 
     */
    public function getTypeFonctionCommissaire()
    {
        return $this->typeFonctionCommissaire;
    }
	
	
	
    
    public function __toString()
	{
		return (String)$this->getLibelle();
	}
}
