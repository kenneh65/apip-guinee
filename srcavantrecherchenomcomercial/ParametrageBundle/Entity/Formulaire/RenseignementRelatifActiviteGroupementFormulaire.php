<?php

namespace ParametrageBundle\Entity\Formulaire;


use Symfony\Component\Validator\Constraints as Assert;

class RenseignementRelatifActiviteGroupementFormulaire
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
	 
    
}
