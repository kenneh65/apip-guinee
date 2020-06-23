<?php

namespace ParametrageBundle\Entity\Formulaire;


use Symfony\Component\Validator\Constraints as Assert;


class AdministrateurFormulaire
{
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
     *      max = 80,
     *      maxMessage = "Le prenom ne doit pas depasser 80 characteres"
     * )
     *
     */
    private $prenoms;

    /**
     * @var \DateTime
	 * @Assert\Regex(pattern="/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",
     *     match=true,
     *     message="date rccm invalide")
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
     *      maxMessage = "L'adresse ne doit pas depasser 250 characteres"
     * )     
     */
    private $adresse;

	
	
    private $fonction;
    

	
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


    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }


    public function setFonction($fonction)
    {
        $this->fonction = $fonction;
        return $this;
    }

 
    public function getFonction()
    {
        return $this->fonction;
    }
    
   
    
   
}
