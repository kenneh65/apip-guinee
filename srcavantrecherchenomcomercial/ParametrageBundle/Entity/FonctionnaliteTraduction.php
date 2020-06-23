<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FonctionnaliteTraduction
 *
 * @ORM\Table(name="fonctionnalitetraduction")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\FonctionnaliteTraductionRepository")
 */
class FonctionnaliteTraduction
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
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Langue")
     * @ORM\JoinColumn(name="idLangue", referencedColumnName="id")
     */
    private $langue;
    
     /**
     * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\Fonctionnalite",inversedBy="fonctionnaliteTraduction")
      * @ORM\JoinColumn(name="idFonctionnalite", referencedColumnName="id")
      */
    private $fonctionnalite;


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
     * @return FonctionnaliteTraduction
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
     * @return FonctionnaliteTraduction
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
     * Set fonctionnalite
     *
     * @param \ParametrageBundle\Entity\Fonctionnalite $fonctionnalite
     * @return FonctionnaliteTraduction
     */
    public function setFonctionnalite(\ParametrageBundle\Entity\Fonctionnalite $fonctionnalite = null)
    {
        $this->fonctionnalite = $fonctionnalite;

        return $this;
    }

    /**
     * Get fonctionnalite
     *
     * @return \ParametrageBundle\Entity\Fonctionnalite 
     */
    public function getFonctionnalite()
    {
        return $this->fonctionnalite;
    }
	
	public function __toString()
	{
		return (String)$this->getLibelle();
	}
}
