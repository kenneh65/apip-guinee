<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Employe
 *
 * @ORM\Table(name="employe")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\EmployeRepository")
 */
class Employe
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
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Genre")
      * @ORM\JoinColumn(name="idGenre", referencedColumnName="id")
      */
    private $sexe;

    /**
     * @var string
     *
     * @ORM\Column(name="matricule", type="string", length=50, nullable=true)
     */
    private $matricule;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEmbauche", type="datetime", nullable=true)
     */
    private $dateEmbauche;

    /**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Pays")
      * @ORM\JoinColumn(name="nationalite", referencedColumnName="id")
      */
    private $nationalite;

    /**
     * @var string
     *
     * @ORM\Column(name="formation", type="string", length=255, nullable=true)
     */
    private $formation;

    /**
     * @var float
     *
     * @ORM\Column(name="dernierSalaire", type="float", nullable=true)
     */
    private $dernierSalaire;
    
        /**
     * @var string
     *
     * @ORM\Column(name="dernierDiplome", type="string", length=255, nullable=true)
     */
    private $dernierDiplome;

    /**
     * @var string
     *
     * @ORM\Column(name="emploiOccupe", type="string", length=255, nullable=true)
     */
    private $emploiOccupe;

    /**
     * @var string
     *
     * @ORM\Column(name="categorieProfessionnel", type="string", length=255, nullable=true)
     */
    private $categorieProfessionnel;

    /**
     * @var \DateTime
	 * @Assert\Date()
     * @Assert\LessThan("-18 years")
     * @ORM\Column(name="dateNaissance", type="date", nullable=true)
     */
    private $dateNaissance;

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
     * @return Employe
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
     * @return Employe
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
     * Set matricule
     *
     * @param string $matricule
     * @return Employe
     */
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;

        return $this;
    }

    /**
     * Get matricule
     *
     * @return string 
     */
    public function getMatricule()
    {
        return $this->matricule;
    }

    /**
     * Set dateEmbauche
     *
     * @param \DateTime $dateEmbauche
     * @return Employe
     */
    public function setDateEmbauche($dateEmbauche)
    {
        $this->dateEmbauche = $dateEmbauche;

        return $this;
    }

    /**
     * Get dateEmbauche
     *
     * @return \DateTime 
     */
    public function getDateEmbauche()
    {
        return $this->dateEmbauche;
    }

    /**
     * Set formation
     *
     * @param string $formation
     * @return Employe
     */
    public function setFormation($formation)
    {
        $this->formation = $formation;

        return $this;
    }

    /**
     * Get formation
     *
     * @return string 
     */
    public function getFormation()
    {
        return $this->formation;
    }

    /**
     * Set dernierSalaire
     *
     * @param float $dernierSalaire
     * @return Employe
     */
    public function setDernierSalaire($dernierSalaire)
    {
        $this->dernierSalaire = $dernierSalaire;

        return $this;
    }

    /**
     * Get dernierSalaire
     *
     * @return float 
     */
    public function getDernierSalaire()
    {
        return $this->dernierSalaire;
    }

    /**
     * Set emploiOccupe
     *
     * @param string $emploiOccupe
     * @return Employe
     */
    public function setEmploiOccupe($emploiOccupe)
    {
        $this->emploiOccupe = $emploiOccupe;

        return $this;
    }

    /**
     * Get emploiOccupe
     *
     * @return string 
     */
    public function getEmploiOccupe()
    {
        return $this->emploiOccupe;
    }

    /**
     * Set categorieProfessionnel
     *
     * @param string $categorieProfessionnel
     * @return Employe
     */
    public function setCategorieProfessionnel($categorieProfessionnel)
    {
        $this->categorieProfessionnel = $categorieProfessionnel;

        return $this;
    }

    /**
     * Get categorieProfessionnel
     *
     * @return string 
     */
    public function getCategorieProfessionnel()
    {
        return $this->categorieProfessionnel;
    }

    /**
     * Set dernierDiplome
     *
     * @param string $dernierDiplome
     * @return Employe
     */
    public function setDernierDiplome($dernierDiplome)
    {
        $this->dernierDiplome = $dernierDiplome;

        return $this;
    }

    /**
     * Get dernierDiplome
     *
     * @return string 
     */
    public function getDernierDiplome()
    {
        return $this->dernierDiplome;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     * @return Employe
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
     * Set dossierDemande
     *
     * @param \BanquemondialeBundle\Entity\DossierDemande $dossierDemande
     * @return Employe
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
     * Set sexe
     *
     * @param \BanquemondialeBundle\Entity\Genre $sexe
     * @return Employe
     */
    public function setSexe(\BanquemondialeBundle\Entity\Genre $sexe = null)
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get sexe
     *
     * @return \BanquemondialeBundle\Entity\Genre 
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set nationalite
     *
     * @param \BanquemondialeBundle\Entity\Pays $nationalite
     * @return Employe
     */
    public function setNationalite(\BanquemondialeBundle\Entity\Pays $nationalite = null)
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    /**
     * Get nationalite
     *
     * @return \BanquemondialeBundle\Entity\Pays 
     */
    public function getNationalite()
    {
        return $this->nationalite;
    }
}
