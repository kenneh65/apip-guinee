<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaysTraduction
 *
 * @ORM\Table(name="paystraduction")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\PaysTraductionRepository")
 */
class PaysTraduction
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
     * @ORM\Column(name="libelle", type="string", length=100)
     */
    private $libelle;
	
	
	/**
     * @var string
     *
     * @ORM\Column(name="nationalite", type="string", length=100)
     */
    private $nationalite;

    
     /**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Pays", inversedBy= "paysTraduction")
      * @ORM\JoinColumn(name="idPays", referencedColumnName="id")
      */ 
    private $pays;
   
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
     * @return PaysTraduction
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
     * Set nationalite
     *
     * @param string $nationalite
     * @return PaysTraduction
     */
    public function setNationalite($nationalite)
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    /**
     * Get nationalite
     *
     * @return string 
     */
    public function getNationalite()
    {
        return $this->nationalite;
    }
	
	
	
    /**
   * Get idPays
   *
   * @return \BanquemondialeBundle\Entity\Pays
   */
    function getIdPays() {
        return $this->idPays;
    }
/**
   * Get idLangue
   *
   * @return \BanquemondialeBundle\Entity\Langue
   */
    function getIdLangue() {
        return $this->idLangue;
    }
/**
     *  Set idPays
     *
     * @param \BanquemondialeBundle\Entity\Formejuridique $idPays
     * @return PaysTraduction
     */
    function setIdPays($idPays) {
        $this->idPays = $idPays;
    }
/**
     *  Set idLangue
     *
     * @param \BanquemondialeBundle\Entity\Formejuridique $idLangue
     * @return PaysTraduction
     */
    function setIdLangue($idLangue) {
        $this->idLangue = $idLangue;
    }



    /**
     * Set pays
     *
     * @param \BanquemondialeBundle\Entity\Pays $pays
     * @return PaysTraduction
     */
    public function setPays(\BanquemondialeBundle\Entity\Pays $pays = null)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return \BanquemondialeBundle\Entity\Pays 
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set langue
     *
     * @param \BanquemondialeBundle\Entity\Langue $langue
     * @return PaysTraduction
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
}
