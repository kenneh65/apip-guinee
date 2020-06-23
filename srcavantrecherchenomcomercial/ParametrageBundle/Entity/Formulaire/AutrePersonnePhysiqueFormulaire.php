<?php

namespace ParametrageBundle\Entity\Formulaire;


use Symfony\Component\Validator\Constraints as Assert;

class AutrePersonnePhysiqueFormulaire
{
    /** 
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 80,
     *      maxMessage = "Le nom ne doit pas depasser 80 characteres"
     * )
     */
    private $nom;

	
    /**
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 80,
     *      maxMessage = "Le prenom ne doit pas depasser 80 characteres"
     * )
     */
    private $prenoms;

	
    /**
     * @var \DateTime
	 * @Assert\Regex(pattern="/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",
     *     match=true,
     *     message="date naissance invalide")
     */
    private $date_naissance;

	
    /**
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 150,
     *      maxMessage = "Le lieu de naissance ne doit pas depasser 150 characteres"
     * )
     */
    private $lieu_naissance;

	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 250,
     *      maxMessage = "La nationalite ne doit pas depasser 250 characteres"
     * )     
     */
    private $nationalite;
	
	
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 250,
     *      maxMessage = "Le domicile ne doit pas depasser 250 characteres"
     * )     
     */
    private $domicile;
	
	
	
	
	/********************
	 * getters setters 	*
	 ********************/


	 
	public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }

    public function getNom()
    {
        return $this->nom;
    }

  
    public function setPrenoms($prenoms)
    {
        $this->prenoms = $prenoms;
        return $this;
    }

    public function getPrenoms()
    {
        return $this->prenoms;
    }


    public function setDateNaissance($date_naissance)
    {
        $this->date_naissance = $date_naissance;
        return $this;
    }

    public function getDateNaissance()
    {
        return $this->date_naissance;
    }

 
    public function setLieuNaissance($lieu_naissance)
    {
        $this->lieu_naissance = $lieu_naissance;
        return $this;
    }

    public function getLieuNaissance()
    {
        return $this->lieu_naissance;
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
	
	
    public function setDomicile($domicile)
    {
        $this->domicile = $domicile;
        return $this;
    }

    public function getDomicile()
    {
        return $this->domicile;
    }


}
