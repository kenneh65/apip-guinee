<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LiensutilesTraduction
 *
 * @ORM\Table(name="liensutiles_traduction")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\LiensutilesTraductionRepository")
 */
class LiensutilesTraduction
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
     * @ORM\Column(name="titre", type="string", length=100)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=100)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Langue")
     */
    private $langue;

    /**
     *@ORM\ManyToOne(targetEntity="Liensutiles",inversedBy="traduction")
     */
    private $lien;

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
     * @return LiensutilesTraduction
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
     * Set url
     *
     * @param string $url
     * @return LiensutilesTraduction
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return LiensutilesTraduction
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

    /**
     * Set langue
     *
     * @param string $langue
     * @return LiensutilesTraduction
     */
    public function setLangue($langue)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return string 
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set lien
     *
     * @param \BanquemondialeBundle\Entity\Liensutiles $lien
     * @return LiensutilesTraduction
     */
    public function setLien(\BanquemondialeBundle\Entity\Liensutiles $lien = null)
    {
        $this->lien = $lien;

        return $this;
    }

    /**
     * Get lien
     *
     * @return \BanquemondialeBundle\Entity\Liensutiles 
     */
    public function getLien()
    {
        return $this->lien;
    }
}
