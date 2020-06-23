<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeDonnee
 *
 * @ORM\Table(name="typedonnee")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\TypeDonneeRepository")
 */
class TypeDonnee
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
     * @ORM\Column(name="typedata", type="string", length=20, unique=true)
     */
    private $typedata;


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
     * @return TypeDonnee
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
     * @return TypeDonnee
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
}
