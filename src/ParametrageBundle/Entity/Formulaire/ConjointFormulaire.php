<?php

namespace ParametrageBundle\Entity\Formulaire;

use Symfony\Component\Validator\Constraints as Assert;


class ConjointFormulaire
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
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 150,
     *      maxMessage = "Le lieu de mariage ne doit pas depasser 150 characteres"
     * )
     */
    private $lieu_mariage;
	
	
	/**
     * @var \DateTime
	 * @Assert\Regex(pattern="/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",
     *     match=true,
     *     message="date mariage invalide")
     */
    private $date_mariage;
	
	
	
	/**
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 250,
     *      maxMessage = "L'option matrimoniale ne doit pas depasser 250 characteres"
     * )
     */
    private $option_matrimoniale;
	
	
	
	/**
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 250,
     *      maxMessage = "Le regime matrimonial ne doit pas depasser 250 characteres"
     * )
     */
    private $regime_matrimonial;
	
	
	/**
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 250,
     *      maxMessage = "Les clauses restrictives ne doit pas depasser 250 characteres"
     * )
     */
    private $clauses_restrictives;

	
	/**
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 250,
     *      maxMessage = "La demande de separation de bien ne doit pas depasser 250 characteres"
     * )
     */
    private $demande_separation_bien;
	

		
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


    public function setPrenom($prenoms)
    {
        $this->prenoms = $prenoms;

        return $this;
    }

    public function getPrenom()
    {
        return $this->prenoms;
    }


    public function setDateMariage($date_mariage)
    {
        $this->date_mariage = $date_mariage;

        return $this;
    }

    public function getDateMariage()
    {
        return $this->date_mariage;
    }
	

    public function setLieuMariage($lieu_mariage)
    {
        $this->lieu_mariage = $lieu_mariage;

        return $this;
    }

    public function getLieuMariage()
    {
        return $this->lieu_mariage;
    }


    public function setRegimeMatrimonial($regime_matrimonial)
    {
        $this->regime_matrimonial = $regime_matrimonial;

        return $this;
    }

    public function getRegimeMatrimonial()
    {
        return $this->regime_matrimonial;
    }
	
	
    public function setOptionMatrimoniale($option_matrimoniale)
    {
        $this->option_matrimoniale = $option_matrimoniale;

        return $this;
    }

    public function getOptionMatrimoniale()
    {
        return $this->option_matrimoniale;
    }

	
	
    public function setClausesRestrictives($clauses_restrictives)
    {
        $this->clauses_restrictives = $clauses_restrictives;

        return $this;
    }


    public function getClausesRestrictives()
    {
        return $this->clauses_restrictives;
    }
	
	

    public function setDemandeSeparationBiens($demande_separation_bien)
    {
        $this->demande_separation_bien = $demande_separation_bien;

        return $this;
    }

    public function getDemandeSeparationBiens()
    {
        return $this->demande_separation_bien;
    }

}
