<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Departement
 *
 * @ORM\Table(name="departement")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\DepartementRepository")
 */
class Departement
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
     * @ORM\Column(name="code", type="string", length=100)
     */
    private $code;
    
       /**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Pays")
      * @ORM\JoinColumn(name="idPays", referencedColumnName="id")
      */
    private $pays;
     /**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Region", inversedBy = "departements")
      */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=100)
     */
    private $libelle;


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
     * Set code
     *
     * @param string $code
     * @return Departement
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
     * Set libelle
     *
     * @param string $libelle
     * @return Departement
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
     * Set Region
     *
     * @param \BanquemondialeBundle\Entity\Region $region
     * @return Departement
     */
    public function setRegion(\BanquemondialeBundle\Entity\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get Region
     *
     * @return \BanquemondialeBundle\Entity\Region 
     */
    public function getRegion()
    {
        return $this->region;
    }
     public function __toString()
	{
		return (String)$this->getLibelle();
	}

    /**
     * Set pays
     *
     * @param \BanquemondialeBundle\Entity\Pays $pays
     * @return Departement
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
}
