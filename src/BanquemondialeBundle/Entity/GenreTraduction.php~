<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GenreTraduction
 *
 * @ORM\Table(name="genretraduction")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\GenreTraductionRepository")
 */
class GenreTraduction
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
     * @ORM\Column(name="libelle", type="string", length=80)
     */
    private $libelle;
	
	
	/**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Genre", cascade={"persist","remove"})
      * @ORM\JoinColumn(name="idGenre", referencedColumnName="id")
      */
    private $genre;
	
	
	/**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Langue", cascade={"persist","remove"})
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
     * @return GenreTraduction
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
     * Set genre
     *
     * @param integer $genre
     * @return GenreTraduction
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return integer 
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set langue
     *
     * @param integer $langue
     * @return GenreTraduction
     */
    public function setLangue($langue)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return integer 
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
