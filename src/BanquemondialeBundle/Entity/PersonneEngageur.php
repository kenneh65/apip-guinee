<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * PersonneEngageur
 *
 * @ORM\Table(name="personneengageur")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\PersonneEngageurRepository")
 */
class PersonneEngageur
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
     * @ORM\Column(name="nom", type="string", length=60)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=120)
     */
    private $prenom;

	/**
     * @var \date
     * @Assert\Date()
     * @Assert\LessThan("-18 years")
	 * @Assert\NotBlank(message="champ_requis")	
     * @ORM\Column(name="dateNaissance", type="date",nullable=false)
     */
    private $dateDeNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="lieuDeNaissance", type="string", length=150, nullable=true)
     */
    private $lieuDeNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="domicile", type="string", length=150, nullable=true)
     */
    private $domicile;
	
	/**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\DossierDemande")
     * @ORM\JoinColumn(name="idDossierDemande", referencedColumnName="id")    
     */
    private $dossierDemande;
	
	/**
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Pays")
      * @ORM\JoinColumn(name="idPays", referencedColumnName="id")
      */ 
    private $pays;


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
     * @return PersonneEngageur
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
     * @return PersonneEngageur
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
     * @return PersonneEngageur
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
     * Set lieuDeNaissance
     *
     * @param string $lieuDeNaissance
     * @return PersonneEngageur
     */
    public function setLieuDeNaissance($lieuDeNaissance)
    {
        $this->lieuDeNaissance = $lieuDeNaissance;

        return $this;
    }

    /**
     * Get lieuDeNaissance
     *
     * @return string 
     */
    public function getLieuDeNaissance()
    {
        return $this->lieuDeNaissance;
    }

    /**
     * Set domicile
     *
     * @param string $domicile
     * @return PersonneEngageur
     */
    public function setDomicile($domicile)
    {
        $this->domicile = $domicile;

        return $this;
    }

    /**
     * Get domicile
     *
     * @return string 
     */
    public function getDomicile()
    {
        return $this->domicile;
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
     * @return PersonneEngageur
     */
    function setDossierDemande($dossierDemande) {
        $this->dossierDemande = $dossierDemande;
    } 
	
	 /**
     * Set pays
     *
     * @param \BanquemondialeBundle\Entity\Pays $pays
     * @return PersonneEngageur
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
}
