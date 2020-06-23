<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeOperation
 *
 * @ORM\Table(name="typeoperation")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\TypeOperationRepository")
 */
class TypeOperation
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function __toString() {
        return (string)$this->getId();
    }
}
