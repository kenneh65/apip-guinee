<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeFonctionCommissaire
 *
 * @ORM\Table(name="typefonctioncommissaire")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\TypeFonctionCommissaireRepository")
 */
class TypeFonctionCommissaire
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
     * @ORM\OneToMany(targetEntity="BanquemondialeBundle\Entity\TypeFonctionCommissaireTraduction", mappedBy="typeFonctionCommissaire")
     */
    private $typeFonctionCommissaireTraduction;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->typeFonctionCommissaireTraduction = new \Doctrine\Common\Collections\ArrayCollection();
    }
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=80)
     */
    private $libelle;

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
     * @return TypeFoncionCommissaire
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
     * @return TypeFonctionCommissaire
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
    
    public function __toString()
	{
		return (String)$this->getLibelle();
	}

    /**
     * Add typeFonctionCommissaireTraduction
     *
     * @param \BanquemondialeBundle\Entity\TypeFonctionCommissaire $typeFonctionCommissaireTraduction
     * @return TypeFonctionCommissaire
     */
    public function addTypeFonctionCommissaireTraduction(\BanquemondialeBundle\Entity\TypeFonctionCommissaire $typeFonctionCommissaireTraduction)
    {
        $this->typeFonctionCommissaireTraduction[] = $typeFonctionCommissaireTraduction;

        return $this;
    }

    /**
     * Remove typeFonctionCommissaireTraduction
     *
     * @param \BanquemondialeBundle\Entity\TypeFonctionCommissaire $typeFonctionCommissaireTraduction
     */
    public function removeTypeFonctionCommissaireTraduction(\BanquemondialeBundle\Entity\TypeFonctionCommissaire $typeFonctionCommissaireTraduction)
    {
        $this->typeFonctionCommissaireTraduction->removeElement($typeFonctionCommissaireTraduction);
    }

    /**
     * Get typeFonctionCommissaireTraduction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTypeFonctionCommissaireTraduction()
    {
        return $this->typeFonctionCommissaireTraduction;
    }
}
