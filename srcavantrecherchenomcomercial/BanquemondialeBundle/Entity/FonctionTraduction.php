<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FonctionTraduction
 *
 * @ORM\Table(name="fonctiontraduction")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\FonctionTraductionRepository")
 */
class FonctionTraduction {

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
     * @ORM\Column(name="libelle", type="string", length=100)
     */
    private $libelle;
	
	/**
     * @var string
     *
     * @ORM\Column(name="libelleFeminin", type="string", length=100)
     */
    private $libelleFeminin;


    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Fonction", inversedBy="fonctionTraduction", cascade={"persist"})
     * @ORM\JoinColumn(name="idFonction", referencedColumnName="id")
     */
    private $fonction;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Langue", cascade={"persist"})
     * @ORM\JoinColumn(name="idLangue", referencedColumnName="id")    
     */
    private $langue;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return FonctionTraduction
     */
    public function setLibelle($libelle) {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle() {
        return $this->libelle;
    }

	/**
     * Set libelle
     *
     * @param string $libelleFeminin
     * @return FonctionTraduction
     */
    public function setLibelleFeminin($libelleFeminin) {
        $this->libelleFeminin = $libelleFeminin;

        return $this;
    }

    /**
     * Get libelleFeminin
     *
     * @return string 
     */
    public function getLibelleFeminin() {
        return $this->libelleFeminin;
    }
	
    /**
     * Get idFonction
     *
     * @return \BanquemondialeBundle\Entity\Fonction
     */
    function getIdFonction() {
        return $this->idFonction;
    }

    /**
     * Get langue
     *
     * @return \BanquemondialeBundle\Entity\Langue
     */
    function getLangue() {
        return $this->langue;
    }

    /**
     *  Set idPays
     *
     * @param \BanquemondialeBundle\Entity\Formejuridique $idPays
     * @return PaysTraduction
     */
    function setIdPays($idPays) {
        $this->idPays = $idPays;
    }

    /**
     *  Set langue
     *
     * @param \BanquemondialeBundle\Entity\Formejuridique $langue
     * @return PaysTraduction
     */
    function setLangue($langue) {
        $this->langue = $langue;
    }

    /**
     * Set fonction
     *
     * @param \BanquemondialeBundle\Entity\Fonction $fonction
     * @return FonctionTraduction
     */
    public function setFonction(\BanquemondialeBundle\Entity\Fonction $fonction = null) {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get fonction
     *
     * @return \BanquemondialeBundle\Entity\Fonction 
     */
    public function getFonction() {
        return $this->fonction;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return FonctionTraduction
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

    public function __toString() {
        return (String) $this->getLibelle();
    }

}
