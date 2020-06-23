<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Langue
 *
 * @ORM\Table(name="langue")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\LangueRepository")
 *  * @UniqueEntity("code", message="language.unique")
 */
class Langue
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
     * @ORM\Column(name="code", type="string", length=3, unique=true)
     *  @Assert\NotBlank(message="langue.not_null_code")
     *  *@Assert\Length(
     *      min = 2,
     *      max = 3,
     *      minMessage = "langue.min_message",
     *      maxMessage = "langue.max_message"
     * )
     */
    private $code;

    
    /**
     * @var string
     *
     * @ORM\Column(name="Libelle", type="string", length=80)
    *  @Assert\NotBlank(message="langue.not_null_label")
      */
    private $libelle;
    
    /**
     * @var boolean
     * @ORM\Column(name="courante",type="boolean")
     */
    private $courante;
 


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
     * @return Langue
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
     * @return Langue
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
     * Set courante
     *
     * @param boolean $courante
     * @return Langue
     */
    public function setCourante($courante)
    {
        $this->courante = $courante;

        return $this;
    }

    /**
     * Get courante
     *
     * @return boolean 
     */
    public function getCourante()
    {
        return $this->courante;
    }

   
}
