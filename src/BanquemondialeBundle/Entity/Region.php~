<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Region
 *
 * @ORM\Table(name="region")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\RegionRepository")
 */
class Region
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
     * @ORM\Column(name="code", type="string", length=2)
     *  
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100)
     *  @Assert\NotBlank(message="region.not_null_label")
     */
    private $libelle;

	 /**
     * @ORM\OneToMany(targetEntity="BanquemondialeBundle\Entity\Departement", mappedBy="region", cascade={"persist"})
     */
     private $departements;

    /**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Pays", inversedBy="regions")
      * @ORM\JoinColumn(name="idPays", referencedColumnName="id")
      */ 
    private $pays;
	
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean", options={"default":true})
     */
    private $actif=true;
    
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
     * @return libelle
     */
    public function setRegion($libelle)
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
   * Get idPays
   *
   * @return \BanquemondialeBundle\Entity\Pays
   */
    function getIdPays() {
        return $this->idPays;
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
     * Set libelle
     *
     * @param string $libelle
     * @return Region
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Set pays
     *
     * @param \BanquemondialeBundle\Entity\Pays $pays
     * @return Region
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
    
    public function getPaysTraduction($locale)
    {
        foreach($this->pays->getPaysTraduction() as $traduction)
            if($traduction->getLangue()->getCode()==$locale)
                return $traduction->getLibelle();
        
         return '-';
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->departements = new \Doctrine\Common\Collections\ArrayCollection();
    }

    

    /**
     * Add departements
     *
     * @param \BanquemondialeBundle\Entity\Departement $departements
     * @return Region
     */
    public function addDepartement(\BanquemondialeBundle\Entity\Departement $departements)
    {
        $this->departements[] = $departements;

        return $this;
    }

    /**
     * Remove departements
     *
     * @param \BanquemondialeBundle\Entity\Departement $departements
     */
    public function removeDepartement(\BanquemondialeBundle\Entity\Departement $departements)
    {
        $this->departements->removeElement($departements);
    }

    /**
     * Get departements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDepartements()
    {
        return $this->departements;
    }
    
    public function __toString()
	{
		return (String)$this->getLibelle();
	}

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return Region
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
    public function isActif()
    {
        return $this->actif;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Region
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

    /**
     * Get actif
     *
     * @return boolean 
     */
    public function getActif()
    {
        return $this->actif;
    }
}
