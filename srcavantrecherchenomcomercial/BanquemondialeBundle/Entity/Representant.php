<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Representant
 *
 * @ORM\Table(name="representant")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\RepresentantRepository")
 */
class Representant
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
     * @ORM\Column(name="dateDeNaissance", type="date")
     */
    private $dateDeNaissance;

    /**
     * @var string
     * 
     * @ORM\Column(name="adresse", type="string", length=250,nullable=true)
     */
    private $adresse;

	 
	 /**
     * @var string
     *
     * @ORM\Column(name="numeroIdentiteNational", type="string", length=50,nullable=true)
     * * @Assert\Length(
     *      min = 1,
     *      max = 35,
     *      minMessage = "longueur_cni_invalide",
     *      maxMessage = "longueur_cni_invalide"
     * )
     */
    private $numeroIdentiteNational;
	
	/**
     * @var string
     * @ORM\Column(name="typeIdentification", type="string",length=50,nullable=true)
     */
    private $typeIdentification;


	/**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=30,nullable=true)
     * @Assert\Regex(pattern="/^(\((\+|00)[0-9]{2,5}\))([0-9 ]{1,20})$/",
     *     match=true,
     *     message="telephone_invalide")
     */
    private $telephone;


	/**
     * @var string
     *
     * @ORM\Column(name="portable", type="string", length=30,nullable=true)
     * @Assert\Regex(pattern="/^(\((\+|00)[0-9]{2,5}\))([0-9 ]{1,20})$/",
     *     match=true,
     *     message="telephone_invalide")
     */
    private $portable;

    /**
     * @var string
     * @Assert\Email
     * @ORM\Column(name="email", type="string", length=50,nullable=true)
     */
    private $email;
	
	
	/**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Genre")
      * @ORM\JoinColumn(name="idGenre", referencedColumnName="id")
      */
    private $genre;
		
	
	/**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\SituationMatrimoniale")
      * @ORM\JoinColumn(name="idSituationMatrimoniale", referencedColumnName="id")
      */
    private $situationMatrimoniale;
	
	  
	/**
	  * @ORM\OneToMany(targetEntity="BanquemondialeBundle\Entity\Conjoint", mappedBy="advert")
	  */
    private $conjoints;
	
	
	/**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Pays")
      * @ORM\JoinColumn(name="idPays", referencedColumnName="id")
      */
    private $pays;
	
		
	
	/**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Fonction")
      * @ORM\JoinColumn(name="idFonction", referencedColumnName="id")
      */
    private $fonction;
	
	
	/**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\DossierDemande")
      * @ORM\JoinColumn(name="idDossierDemande", referencedColumnName="id")
      */
    private $dossierDemande;
	

	/**
     * @var string
     *
     * @ORM\Column(name="lieuNaissance", type="string", length=150,nullable=true)
     */
    private $lieuNaissance;
	
	/**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Civilite")
     * @ORM\JoinColumn(name="idCivilite", referencedColumnName="id")
     */
    private $civilite;
	
	/**
     * @var string
     *
	 * @Assert\Length(max = 100)
     * @ORM\Column(name="ville", type="string", length=100,nullable=true)
     */
    private $ville;
	
	
	/**
     * @var string
     *
	 * @Assert\Length(max = 150)
     * @ORM\Column(name="quartier", type="string", length=150,nullable=true)
     */
    private $quartier;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->conjoints = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return Representant
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
     * @return Representant
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
     * Set dateDeNaissance
     *
     * @param \DateTime $dateDeNaissance
     * @return Representant
     */
    public function setDateDeNaissance($dateDeNaissance)
    {
        $this->dateDeNaissance = $dateDeNaissance;

        return $this;
    }

    /**
     * Get dateDeNaissance
     *
     * @return \DateTime 
     */
    public function getDateDeNaissance()
    {
        return $this->dateDeNaissance;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return Representant
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
     * Set numeroIdentiteNational
     *
     * @param string $numeroIdentiteNational
     * @return Representant
     */
    public function setNumeroIdentiteNational($numeroIdentiteNational)
    {
        $this->numeroIdentiteNational = $numeroIdentiteNational;

        return $this;
    }

    /**
     * Get numeroIdentiteNational
     *
     * @return string 
     */
    public function getNumeroIdentiteNational()
    {
        return $this->numeroIdentiteNational;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return Representant
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set portable
     *
     * @param string $portable
     * @return Representant
     */
    public function setPortable($portable)
    {
        $this->portable = $portable;

        return $this;
    }

    /**
     * Get portable
     *
     * @return string 
     */
    public function getPortable()
    {
        return $this->portable;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Representant
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set genre
     *
     * @param \BanquemondialeBundle\Entity\Genre $genre
     * @return Representant
     */
    public function setGenre(\BanquemondialeBundle\Entity\Genre $genre = null)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return \BanquemondialeBundle\Entity\Genre 
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set situationMatrimoniale
     *
     * @param \BanquemondialeBundle\Entity\SituationMatrimoniale $situationMatrimoniale
     * @return Representant
     */
    public function setSituationMatrimoniale(\BanquemondialeBundle\Entity\SituationMatrimoniale $situationMatrimoniale = null)
    {
        $this->situationMatrimoniale = $situationMatrimoniale;

        return $this;
    }

    /**
     * Get situationMatrimoniale
     *
     * @return \BanquemondialeBundle\Entity\SituationMatrimoniale 
     */
    public function getSituationMatrimoniale()
    {
        return $this->situationMatrimoniale;
    }

    /**
     * Add conjoints
     *
     * @param \BanquemondialeBundle\Entity\Conjoint $conjoints
     * @return Representant
     */
    public function addConjoint(\BanquemondialeBundle\Entity\Conjoint $conjoints)
    {
        $this->conjoints[] = $conjoints;

        return $this;
    }

    /**
     * Remove conjoints
     *
     * @param \BanquemondialeBundle\Entity\Conjoint $conjoints
     */
    public function removeConjoint(\BanquemondialeBundle\Entity\Conjoint $conjoints)
    {
        $this->conjoints->removeElement($conjoints);
    }

    /**
     * Get conjoints
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConjoints()
    {
        return $this->conjoints;
    }

    /**
     * Set pays
     *
     * @param \BanquemondialeBundle\Entity\Pays $pays
     * @return Representant
     */
    public function setPays(\BanquemondialeBundle\Entity\Pays $pays = null)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return \BanquemondialeBundle\Entity\Pays 
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set fonction
     *
     * @param \BanquemondialeBundle\Entity\Fonction $fonction
     * @return Representant
     */
    public function setFonction(\BanquemondialeBundle\Entity\Fonction $fonction = null)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get fonction
     *
     * @return \BanquemondialeBundle\Entity\Fonction 
     */
    public function getFonction()
    {
        return $this->fonction;
    }

    /**
     * Set dossierDemande
     *
     * @param \BanquemondialeBundle\Entity\DossierDemande $dossierDemande
     * @return Representant
     */
    public function setDossierDemande(\BanquemondialeBundle\Entity\DossierDemande $dossierDemande = null)
    {
        $this->dossierDemande = $dossierDemande;

        return $this;
    }

    /**
     * Get dossierDemande
     *
     * @return \BanquemondialeBundle\Entity\DossierDemande 
     */
    public function getDossierDemande()
    {
        return $this->dossierDemande;
    }
	
	/**
     * Set typeIdentification
     *
     * @param string $typeIdentification
     * @return Representant
     */
    public function setTypeIdentification($typeIdentification)
    {
        $this->typeIdentification = $typeIdentification;

        return $this;
    }

    /**
     * Get typeIdentification
     *
     * @return string 
     */
    public function getTypeIdentification()
    {
        return $this->typeIdentification;
    }
	
	 /**
     * Set lieuNaissance
     *
     * @param string $lieuNaissance
     * @return Representant
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
     * Set civilite
     *
     * @param integer $civilite
     * @return Representant
     */
    public function setCivilite($civilite)
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * Get civilite
     *
     * @return integer 
     */
    public function getCivilite()
    {
        return $this->civilite;
    }
	
	
	/**
     * Set ville
     *
     * @param string $ville
     * @return Representant
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string 
     */
    public function getVille()
    {
        return $this->ville;
    }
	
	
	/**
     * Set quartier
     *
     * @param string $quartier
     * @return Representant
     */
    public function setQuartier($quartier)
    {
        $this->quartier = $quartier;

        return $this;
    }

    /**
     * Get quartier
     *
     * @return string 
     */
    public function getQuartier()
    {
        return $this->quartier;
    }
	
	
}
