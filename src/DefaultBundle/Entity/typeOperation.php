<?php

namespace DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * typeOperation
 *
 * @ORM\Table(name="type_operation")
 * @ORM\Entity(repositoryClass="DefaultBundle\Repository\typeOperationRepository")
 */
class typeOperation
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="encour", type="boolean", nullable=true)
     */
    private $encour;


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
     * Set name
     *
     * @param string $name
     * @return typeOperation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set encour
     *
     * @param boolean $encour
     * @return typeOperation
     */
    public function setEncour($encour)
    {
        $this->encour = $encour;

        return $this;
    }

    /**
     * Get encour
     *
     * @return boolean 
     */
    public function getEncour()
    {
        return $this->encour;
    }
}
