<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategorieActiviteTraduction
 *
 * @ORM\Table(name="categorieactivitetraduction")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\CategorieActiviteTraductionRepository")
 */
class CategorieActiviteTraduction
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
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\CategorieActivite",inversedBy="categorieActiviteTraduction")
      * @ORM\JoinColumn(name="idCategorieActivite", referencedColumnName="id")
      */
    private $categorieActivite;


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
     * @return CategorieActiviteTraduction
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
     * @return CategorieActiviteTraduction
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
     * Set secteurActivite
     *
     * @param \ParametrageBundle\Entity\CategorieActivite $categorieActivite
     * @return CategorieActiviteTraduction
     */
    public function setCategorieActivite(\BanquemondialeBundle\Entity\CategorieActivite $categorieActivite = null)
    {
        $this->categorieActivite = $categorieActivite;

        return $this;
    }

    /**
     * Get categorieActivite
     *
     * @return \BanquemondialeBundle\Entity\CategorieActivite 
     */
    public function getCategorieActivite()
    {
        return $this->categorieActivite;
    }
	
	public function __toString()
	{
		return (String)$this->getLibelle();
	}
}
