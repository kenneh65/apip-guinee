<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Administrateur
 *
 * @ORM\Table(name="administrateur")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\AdministrateurRepository")
 */
class Administrateur
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
     * @ORM\Column(name="nom", type="string", length=80)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=80)
     */
    private $prenom;

    /**
     * @var \DateTime
     * @Assert\Date()
     * @Assert\LessThan("-18 years")
     * @ORM\Column(name="dateNaissance", type="date")
     */
    private $dateNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="lieuNaissance", type="string", length=150)
     */
    private $lieuNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=250, nullable=true)
     */
    private $adresse = null;

    /**
     * @var int
     * @Assert\Range(min = 1)
     * @ORM\Column(name="dureeMandat", type="integer")
     */
    private $dureeMandat;
    
    
	
     /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\DossierDemande")
     * @ORM\JoinColumn(name="idDossierDemande", referencedColumnName="id")    
     */
    private $dossierDemande;
   

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
     * @return Administrateur
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
     * @return Administrateur
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
     * @return Administrateur
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
     * Set lieuNaissance
     *
     * @param string $lieuNaissance
     * @return Administrateur
     */
    public function setLieuNaissance($lieuNaissance)
    {
        $this->lieuNaissance = $lieuNaissance;

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
     * Set adresse
     *
     * @param string $adresse
     * @return Administrateur
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

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

    /**
     * Set dureeMandat
     *
     * @param integer $dureeMandat
     * @return Administrateur
     */
    public function setDureeMandat($dureeMandat)
    {
        $this->dureeMandat = $dureeMandat;

        return $this;
    }

    /**
     * Get dureeMandat
     *
     * @return integer 
     */
    public function getDureeMandat()
    {
        return $this->dureeMandat;
    }
    
   
    
     /**
     * Get dossierDemande
     *
     * @return integer 
     */
    function getDossierDemande() {
        return $this->dossierDemande;
    }
    
    /**
     * Set dossierDemande
     *
     * @param integer $dossierDemande
     * @return DossierDemande
     */
    function setDossierDemande($dossierDemande) {
        $this->dossierDemande = $dossierDemande;
    }   

   
}
