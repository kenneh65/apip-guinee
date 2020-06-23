<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SecteurActiviteTraduction
 *
 * @ORM\Table(name="secteuractivitetraduction")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\SecteurActiviteTraductionRepository")
 */
class SecteurActiviteTraduction
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
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\SecteurActivite",inversedBy="secteurActiviteTraduction")
      * @ORM\JoinColumn(name="idSecteurActivite", referencedColumnName="id")
      */
    private $secteurActivite;


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
     * @return SecteurActiviteTraduction
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
     * @return SecteurActiviteTraduction
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
     * @param \ParametrageBundle\Entity\SecteurActivite $secteurActivite
     * @return SecteurActiviteTraduction
     */
    public function setSecteurActivite(\BanquemondialeBundle\Entity\SecteurActivite $secteurActivite = null)
    {
        $this->secteurActivite = $secteurActivite;

        return $this;
    }

    /**
     * Get secteurActivite
     *
     * @return \BanquemondialeBundle\Entity\SecteurActivite 
     */
    public function getSecteurActivite()
    {
        return $this->secteurActivite;
    }
	
	public function __toString()
	{
		return (String)$this->getLibelle();
	}
}
