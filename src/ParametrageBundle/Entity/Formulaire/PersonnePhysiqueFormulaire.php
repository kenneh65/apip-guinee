<?php

namespace ParametrageBundle\Entity\Formulaire;


use Symfony\Component\Validator\Constraints as Assert;

class PersonnePhysiqueFormulaire
{
    
	
	/** 
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 5,
     *      maxMessage = "Le titre ne doit pas depasser 5 characteres"
     * )
     *
     */
    private $titre;
	
	/** 
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 80,
     *      maxMessage = "Le nom ne doit pas depasser 80 characteres"
     * )
     *
     */
    private $nom;

    /**
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le prenom ne doit pas depasser 100 characteres"
     * )
     *
     */
    private $prenoms;


    /**
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Le lieu de naissance ne doit pas depasser 255 characteres"
     * )
     */
    private $date_lieu_naissance;
	
	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 50,
     *      maxMessage = "La nationalite ne doit pas depasser 50 characteres"
     * )     
     */
    private $nationalite;
	

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 250,
     *      maxMessage = "L'adresse ne doit pas depasser 250 characteres"
     * )     
     */
    private $adresse;
	
	
	    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 100,
     *      maxMessage = "L'adresse postale ne doit pas depasser 100 characteres"
     * )     
     */
    private $adresse_postale;
	
	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 50,
     *      maxMessage = "Le numero de telephone ne doit pas depasser 50 characteres"
     * )     
     */
    private $telephone;
	
	
	 /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Le domicile personnel ne doit pas depasser 255 characteres"
     * )     
     */
    private $domicile_personnel;
	
	
	
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 50,
     *      maxMessage = "La ville ne doit pas depasser 50 characteres"
     * )     
     */
    private $ville;
	
	
	
    private $quartier;
     private $commune;
	
	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 50,
     *      maxMessage = "L'email ne doit pas depasser 50 characteres"
     * )     
     */
    private $email;
	
	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 50,
     *      maxMessage = "La situation matrimoniale ne doit pas depasser 50 characteres"
     * )     
     */
    private $situation_matrimoniale;
	
	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 50,
     *      maxMessage = "Les autres precisions ne doit pas depasser 50 characteres"
     * )     
     */
    private $autres_precisions;
	
	
	
	private $conjoints;
	
	
	/********************
	 * getters setters 	*
	 ********************/


	 
	public function setTitre($titre)
    {
        $this->titre = $titre;
        return $this;
    }

    public function getTitre()
    {
        return $this->titre;
    }
	 
	 
	public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }

    public function getNom()
    {
        return $this->nom;
    }

  
    public function setPrenom($prenoms)
    {
        $this->prenoms = $prenoms;
        return $this;
    }

    public function getPrenom()
    {
        return $this->prenoms;
    }


    public function setDateLieuNaissance($date_lieu_naissance)
    {
        $this->date_lieu_naissance = $date_lieu_naissance;
        return $this;
    }

    public function getDateLieuNaissance()
    {
        return $this->date_lieu_naissance;
    }

 
	public function setNationalite($nationalite)
    {
        $this->nationalite = $nationalite;
        return $this;
    }

    public function getNationalite()
    {
        return $this->nationalite;
    }
    
	

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }
	
	
	public function setAdressePostale($adresse_postale)
    {
        $this->adresse_postale = $adresse_postale;
        return $this;
    }

    public function getAdressePostale()
    {
        return $this->adresse_postale;
    }
	

	public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }
	
	
	
	 public function setDomicilePersonnel($domicile_personnel)
    {
        $this->domicile_personnel = $domicile_personnel;
        return $this;
    }

    public function getDomicilePersonnel()
    {
        return $this->domicile_personnel;
    }
	
	public function setVille($ville)
    {
        $this->ville = $ville;
        return $this;
    }

    public function getVille()
    {
        return $this->ville;
    }
	
			
	public function setQuartier($quartier)
    {
        $this->quartier = $quartier;
        return $this;
    }

    public function getQuartier()
    {
        return $this->quartier;
    }
	
	
	
	public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }
	

	
	public function setSituationMatrimoniale($situation_matrimoniale)
    {
        $this->situation_matrimoniale = $situation_matrimoniale;
        return $this;
    }

    public function getSituationMatrimoniale()
    {
        return $this->situation_matrimoniale;
    }
	
    
	
	public function setAutresPrecisions($autres_precisions)
    {
        $this->autres_precisions = $autres_precisions;
        return $this;
    }

    public function getAutresPrecisions()
    {
        return $this->autres_precisions;
    }
	
	
		public function setConjoints(array $conjoints)
    {
        $this->conjoints = $conjoints;
    }

    public function getConjoints()
    {
        return $this->conjoints;
    }
    function getCommune() {
        return $this->commune;
    }

    function setCommune($commune) {
        $this->commune = $commune;
    }
}
