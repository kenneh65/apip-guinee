<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pays
 *
 * @ORM\Table(name="coordonnees")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\CoordonneesRepository")
 */
class Coordonnees
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
     * @ORM\Column(name="code", type="string", length=3)
     */
    private $code;
	
	/**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", options={"default":180})
     */
    private $longitude;
	
	/**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", options={"default":90})
     */
    private $latitude;

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
     * @return Pays
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
	
	public function __toString()
	{
		return (String)$this->getCode();
	}
    
}
