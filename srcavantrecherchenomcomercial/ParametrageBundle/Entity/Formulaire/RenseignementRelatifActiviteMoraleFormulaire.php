<?php

namespace ParametrageBundle\Entity\Formulaire;


use Symfony\Component\Validator\Constraints as Assert;

class RenseignementRelatifActiviteMoraleFormulaire
{
    	

	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Le groupe d'activite principale ne doit pas depasser 255 characteres"
     * )
     */
	private	$groupe_activite_principale;

	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "L'activite principale ne doit pas depasser 255 characteres"
     * )
     */
    private $activite_principale;
		
	
	/**
     * @var string
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Le groupe d'activite secondaire 1 ne doit pas depasser 255 characteres"
     * )
     */
	private	$groupe_activite_secondaire_1;
	
	
	/**
     * @var string
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "L'activite secondaire 1 ne doit pas depasser 255 characteres"
     * )
     */
    private $activite_secondaire_1;
	
	
	/**
     * @var string
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Le groupe d'activite secondaire 2 ne doit pas depasser 255 characteres"
     * )
     */
	private	$groupe_activite_secondaire_2;
	
	/**
     * @var string
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "L'activite secondaire 2 ne doit pas depasser 255 characteres"
     * )
     */
    private $activite_secondaire_2;
	

	/**
     * @var \DateTime
	 * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Regex(pattern="/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",
     *     match=true,
     *     message="date debut invalide")
     */
    private $date_debut;
	
	
	/**
     * @var integer
	 * @Assert\NotNull()
     */    
    private $nb_salaries;
	
	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 100,
     *      maxMessage = "L'etablissement principal ne doit pas depasser 100 characteres"
     * )
     */
    private $etablissement_principal_succursal;
	
	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 100,
     *      maxMessage = "L'origine ne doit pas depasser 100 characteres"
     * )
     */
    private $origine;
	
	
	
	/**
     * @var string
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "L'adresse ne doit pas depasser 255 characteres"
     * )
     */
    private $adresse;
	
	
	
	/**
     * @var string
	 * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le nom du precedent exploitant ne doit pas depasser 100 characteres"
     * )
     */
    private $nom_precedent_exploitant;
	

	/**
     * @var string
	 * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le prenom du precedent exploitant ne doit pas depasser 100 characteres"
     * )
     */
    private $prenom_precedent_exploitant;

	
	/**
     * @var string
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "L'adresse du precedent ne doit pas depasser 255 characteres"
     * )
     */
    private $adresse_precedent_exploitant;
	
	
	/**
     * @var string
	 * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le rccm du precedent exploitant ne doit pas depasser 100 characteres"
     * )
     */
    private $rccm_precedent_exploitant;
	
	
	/**
     * @var string
	 * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le loueur de fond du precedent exploitant ne doit pas depasser 100 characteres"
     * )
     */
    private $loueur_de_fond_precedent_exploitant;


	/**
     * @var string
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "L'adresse de l'etablissement secondaire ne doit pas depasser 255 characteres"
     * )
     */
    private $adresse_etablisement_secondaire;


	
	/**
     * @var string
	 * @Assert\Length(
     *      max = 100,
     *      maxMessage = "L'activite de l'etablissement secondaire ne doit pas depasser 100 characteres"
     * )
     */
    private $activite_etablisement_secondaire;

	



	
	/********************
	 * getters setters 	*
	 ********************/


	 
	public function setGroupeActivitePrincipale($groupe_activite_principale)
    {
        $this->groupe_activite_principale = $groupe_activite_principale;
        return $this;
    }

    public function getGroupeActivitePrincipale()
    {
        return $this->groupe_activite_principale;
    }
	
	
	public function setGroupeActiviteSecondaire1($groupe_activite_secondaire_1)
    {
        $this->groupe_activite_secondaire_1 = $groupe_activite_secondaire_1;
        return $this;
    }

    public function getGroupeActiviteSecondaire1()
    {
        return $this->groupe_activite_secondaire_1;
    }

	
	public function setGroupeActiviteSecondaire2($groupe_activite_secondaire_2)
    {
        $this->groupe_activite_secondaire_2 = $groupe_activite_secondaire_2;
        return $this;
    }

    public function getGroupeActiviteSecondaire2()
    {
        return $this->groupe_activite_secondaire_2;
    }
	
	 
	public function setActivitePrincipale($activite_principale)
    {
        $this->activite_principale = $activite_principale;
        return $this;
    }

    public function getActivitePrincipale()
    {
        return $this->activite_principale;
    }
	
	
	public function setActiviteSecondaire1($activite_secondaire_1)
    {
        $this->activite_secondaire_1 = $activite_secondaire_1;
        return $this;
    }

    public function getActiviteSecondaire1()
    {
        return $this->activite_secondaire_1;
    }
	
	
	public function setActiviteSecondaire2($activite_secondaire_2)
    {
        $this->activite_secondaire_2 = $activite_secondaire_2;
        return $this;
    }

    public function getActiviteSecondaire2()
    {
        return $this->activite_secondaire_2;
    }
	
	
	public function setDateDebut($date_debut)
    {
        $this->date_debut = $date_debut;
        return $this;
    }

    public function getDateDebut()
    {
        return $this->date_debut;
    }
	
	
	public function setNbSalaries($nb_salaries)
    {
        $this->nb_salaries = $nb_salaries;
        return $this;
    }

    public function getNbSalaries()
    {
        return $this->nb_salaries;
    }
	 
	
	public function setEtablissementPrincipalOuSuccursale($etablissement_principal_succursal)
    {
        $this->etablissement_principal_succursal = $etablissement_principal_succursal;
        return $this;
    }

    public function getEtablissementPrincipalOuSuccursale()
    {
        return $this->etablissement_principal_succursal;
    }

	
	public function setOrigine($origine)
    {
        $this->origine = $origine;
        return $this;
    }

    public function getOrigine()
    {
        return $this->origine;
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

	
	public function setNomPrecedentExploitant($nom_precedent_exploitant)
    {
        $this->nom_precedent_exploitant = $nom_precedent_exploitant;
        return $this;
    }

    public function getNomPrecedentExploitant()
    {
        return $this->nom_precedent_exploitant;
    }
	

	
	public function setPrenomPrecedentExploitant($prenom_precedent_exploitant)
    {
        $this->prenom_precedent_exploitant = $prenom_precedent_exploitant;
        return $this;
    }

    public function getPrenomPrecedentExploitant()
    {
        return $this->prenom_precedent_exploitant;
    }

	
	public function setAdressePrecedentExploitant($adresse_precedent_exploitant)
    {
        $this->adresse_precedent_exploitant = $adresse_precedent_exploitant;
        return $this;
    }

    public function getAdressePrecedentExploitant()
    {
        return $this->adresse_precedent_exploitant;
    }
	
	
	public function setRccmPrecedentExploitant($rccm_precedent_exploitant)
    {
        $this->rccm_precedent_exploitant = $rccm_precedent_exploitant;
        return $this;
    }

    public function getRccmPrecedentExploitant()
    {
        return $this->rccm_precedent_exploitant;
    }

	
	public function setLoueurDeFond($loueur_de_fond_precedent_exploitant)
    {
        $this->loueur_de_fond_precedent_exploitant = $loueur_de_fond_precedent_exploitant;
        return $this;
    }

    public function getLoueurDeFond()
    {
        return $this->loueur_de_fond_precedent_exploitant;
    }

	
	public function setAdresseEtablissementSecondaire($adresse_etablisement_secondaire)
    {
        $this->adresse_etablisement_secondaire = $adresse_etablisement_secondaire;
        return $this;
    }

    public function getAdresseEtablissementSecondaire()
    {
        return $this->adresse_etablisement_secondaire;
    }

	
	public function setActiviteEtablissementSecondaire($activite_etablisement_secondaire)
    {
        $this->activite_etablisement_secondaire = $activite_etablisement_secondaire;
        return $this;
    }

    public function getActiviteEtablissementSecondaire()
    {
        return $this->activite_etablisement_secondaire;
    }
    
}
