<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentationTraduction
 *
 * @ORM\Table(name="documentation_traduction")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\DocumentationTraductionRepository")
 */
class DocumentationTraduction {

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
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

   
    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Langue")
     */
    private $langue;

    /**
     * @ORM\ManyToOne(targetEntity="Documentation",inversedBy="traduction")
     */
    private $documentation;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return DocumentationTraduction
     */
    public function setTitre($titre) {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre() {
        return $this->titre;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return DocumentationTraduction
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }


    
    /**
     * Set langue
     *
     * @param \BanquemondialeBundle\Entity\Langue $langue
     * @return DocumentationTraduction
     */
    public function setLangue(\BanquemondialeBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return \BanquemondialeBundle\Entity\Langue 
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set documentation
     *
     * @param \BanquemondialeBundle\Entity\Documentation $documentation
     * @return DocumentationTraduction
     */
    public function setDocumentation(\BanquemondialeBundle\Entity\Documentation $documentation = null)
    {
        $this->documentation = $documentation;

        return $this;
    }

    /**
     * Get documentation
     *
     * @return \BanquemondialeBundle\Entity\Documentation 
     */
    public function getDocumentation ()
    {
        return $this->documentation;
    }
    public function __construct()
    {
        $this->description='';
        $this->titre='';
    }
}
