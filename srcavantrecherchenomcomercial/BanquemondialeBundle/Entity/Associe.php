<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Associe
 *
 * @ORM\Table(name="associe")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\AssocieRepository")
 */
class Associe
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=80, nullable=true)
     */
    private $prenom;

    /**
     * @var \DateTime
	 * @Assert\Date()
     * @ORM\Column(name="dateNaissance", type="date", nullable=true)
     */
    private $dateNaissance;
    
     /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\DossierDemande")
     * @ORM\JoinColumn(name="idDossierDemande", referencedColumnName="id")    
     */
    private $dossierDemande;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=250)
     */
    private $adresse=null;

    /**
     * @var string
     *
     * @ORM\Column(name="lieuNaissance", type="string", length=250, nullable=true)
     */
    private $lieuNaissance=null;
	
	/**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Pays")
     * @ORM\JoinColumn(name="idPays", referencedColumnName="id", nullable=true)    
     */
    private $pays;
	
	/**
     * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\TypeEntreprise")
     * @ORM\JoinColumn(name="idtypeEntreprise", referencedColumnName="id", nullable=true)    
     */
    private $typeEntreprise;


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
     * Set nom
     *
     * @param string $nom
     * @return Associe
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return Associe
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     * @return Associe
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime 
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return Associe
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get lieuNaissance
     *
     * @return string 
     */
    public function getLieuNaissance()
    {
        return $this->lieuNaissance;
    }

    /**
     * Set lieuNaissance
     *
     * @param string $lieuNaissance
     * @return Associe
     */
    public function setLieuNaissance($lieuNaissance)
    {
        $this->lieuNaissance = $lieuNaissance;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string 
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    
    function getDossierDemande() {
        return $this->dossierDemande;
    }

    function setDossierDemande(\BanquemondialeBundle\Entity\DossierDemande $dossierDemande = null) {
        $this->dossierDemande = $dossierDemande;
    }
    
    function getPays() {
        return $this->pays;
    }

    function setPays(\BanquemondialeBundle\Entity\Pays $pays = null) {
        $this->pays = $pays;
    }
    
    function getTypeEntreprise() {
        return $this->typeEntreprise;
    }

    function setTypeEntreprise(\ParametrageBundle\Entity\TypeEntreprise $typeEntreprise = null) {
        $this->typeEntreprise = $typeEntreprise;
    }
}
