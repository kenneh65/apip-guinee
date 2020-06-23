<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeFormulaire
 *
 * @ORM\Table(name="typeformulaire")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\TypeFormulaireRepository")
 */
class TypeFormulaire
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
     * @ORM\Column(name="libelle", type="string", length=50, unique=true)
     */
    private $libelle;


    /**
     * @var string
     *
     * @ORM\Column(name="typemysql", type="string", length=20,nullable=true)
     */
    private $typemysql;
	
	
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
     * Set typedata
     *
     * @param string $typedata
     * @return TypeFormulaire
     */
    public function setTypedata($typedata)
    {
        $this->typedata = $typedata;

        return $this;
    }

    /**
     * Get typedata
     *
     * @return string 
     */
    public function getTypedata()
    {
        return $this->typedata;
    }
    
    public function __toString() {
        return $this->getTypedata();
    }

    /**
     * Set typemysql
     *
     * @param string $typemysql
     * @return TypeFormulaire
     */
    public function setTypemysql($typemysql)
    {
        $this->typemysql = $typemysql;

        return $this;
    }

    /**
     * Get typemysql
     *
     * @return string 
     */
    public function getTypemysql()
    {
        return $this->typemysql;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return TypeFormulaire
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
}
