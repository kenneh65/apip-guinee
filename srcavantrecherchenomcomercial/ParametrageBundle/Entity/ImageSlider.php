<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageSlider
 *
 * @ORM\Table(name="image_slider")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\ImageSliderRepository")
 */
class ImageSlider
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
     * @ORM\Column(name="titre", type="string", length=100)
     */
    private $titre;

     /**
     * @var string
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;


    /**
     * @var bool
     *
     * @ORM\Column(name="est_active", type="boolean")
     */
    private $estActive;

    /**
     * @ORM\OneToOne(targetEntity="BanquemondialeBundle\Entity\Media", inversedBy="imageSlider",cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true,onDelete="cascade")
       */
    private $fichier;


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
     * Set titre
     *
     * @param string $titre
     * @return ImageSlider
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set estActive
     *
     * @param boolean $estActive
     * @return ImageSlider
     */
    public function setEstActive($estActive)
    {
        $this->estActive = $estActive;

        return $this;
    }

    /**
     * Get estActive
     *
     * @return boolean 
     */
    public function getEstActive()
    {
        return $this->estActive;
    }

    /**
     * Set fichier
     *
     * @param \BanquemondialeBundle\Entity\Media $fichier
     * @return ImageSlider
     */
    public function setFichier(\BanquemondialeBundle\Entity\Media $fichier = null)
    {
        $this->fichier = $fichier;

        return $this;
    }

    /**
     * Get fichier
     *
     * @return \BanquemondialeBundle\Entity\Media 
     */
    public function getFichier()
    {
        return $this->fichier;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ImageSlider
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function __construct()
    {
        $this->estActive = true;
    }
}
