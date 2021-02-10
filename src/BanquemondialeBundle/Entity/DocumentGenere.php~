<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentGenere
 *
 * @ORM\Table(name="documentgenere")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\DocumentGenereRepository")
 */
class DocumentGenere
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
     * @ORM\Column(name="libelle", type="string", length=250, nullable=true)
     */
    private $libelle;
	
	/**
     * @var int
     *
     * @ORM\Column(name="idPole", type="integer",nullable=true)
     */
    private $idPole;


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
     * @return DocumentGenere
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
     * Get idPole
     *
     * @return integer 
     */
    public function getIdPole() {
        return $this->idPole;
    }
	
	
	
	    /**
     * Set idPole
     *
     * @param integer $idPole
     * @return DocumentCollected
     */
    public function setIdPole($idPole) {
        $this->idPole = $idPole;

        return $this;
    }
	
	
}
