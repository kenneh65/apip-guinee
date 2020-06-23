<?php

namespace ParametrageBundle\Entity\Formulaire;


use Symfony\Component\Validator\Constraints as Assert;

class PersonneMoraleFormulaire
{
  
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 100,
     *      maxMessage = "La denomination ne doit pas depasser 100 characteres"
     * )
     */
    private $denomination; /*-denominationSociale-*/
	
	
	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le nom commercial ne doit pas depasser 100 characteres"
     * )
     */
    private $nom_commercial; /*-denominationSociale-*/
	
	
	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "L'adresse ne doit pas depasser 255 characteres"
     * )
     */
    private $adresse;
	
	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "L'adresse siege ne doit pas depasser 255 characteres"
     * )
     */
    private $adresse_siege;
	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "L'adresse de l'etablissement ne doit pas depasser 255 characteres"
     * )
     */
    private $adresse_etablissement;
	
	
	/**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 255,
     *      maxMessage = "La forme juridique ne doit pas depasser 255 characteres"
     * )
     */
    private $forme_juridique;
	
	
	/**
     * @var string
	 * @Assert\Length(
     *      max = 100,
     *      maxMessage = "La rccm siege ne doit pas depasser 100 characteres"
     * )
     */
    private $rccm_siege;
	
	
	
	/**
     * @var integer
     */    
    private $capital_social;
	
	/**
     * @var integer
     */    
    private $dont_numeraire;
	
	
	/**
     * @var integer
     */    
    private $dont_en_nature;
	
	/**
     * @var integer
	 * @Assert\NotNull()
     */    
    private $duree;
	

	
	/********************
	 * getters setters 	*
	 ********************/

	public function setDenomination($denomination)
    {
        $this->denomination = $denomination;
        return $this;
    }

    public function getDenomination()
    {
        return $this->denomination;
    }
	
	public function setNomCommercial($nom_commercial)
    {
        $this->nom_commercial = $nom_commercial;
        return $this;
    }

    public function getNomCommercial()
    {
        return $this->nom_commercial;
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
	
	
	public function setAdresseSiege($adresse_siege)
    {
        $this->adresse_siege = $adresse_siege;
        return $this;
    }

    public function getAdresseSiege()
    {
        return $this->adresse_siege;
    }
	
	
	public function setAdresseEtablissement($adresse_etablissement)
    {
        $this->adresse_etablissement = $adresse_etablissement;
        return $this;
    }

    public function getAdresseEtablissement()
    {
        return $this->adresse_etablissement;
    }
	
	
	public function setFormeJuridique($forme_juridique)
    {
        $this->forme_juridique = $forme_juridique;
        return $this;
    }

    public function getFormeJuridique()
    {
        return $this->forme_juridique;
    }

	
	public function setRccmSiege($rccm_siege)
    {
        $this->rccm_siege = $rccm_siege;
        return $this;
    }

    public function getRccmSiege()
    {
        return $this->rccm_siege;
    }
		
	
	public function setCapitalSocial($capital_social)
    {
        $this->capital_social = $capital_social;
        return $this;
    }

    public function getCapitalSocial()
    {
        return $this->capital_social;
    }
	
	
	public function setDontNumeraire($dont_numeraire)
    {
        $this->dont_numeraire = $dont_numeraire;
        return $this;
    }

    public function getDontNumeraire()
    {
        return $this->dont_numeraire;
    }
	
	
	
	public function setDontNature($dont_en_nature)
    {
        $this->dont_en_nature = $dont_en_nature;
        return $this;
    }

    public function getDontNature()
    {
        return $this->dont_en_nature;
    }

	
	public function setDuree($duree)
    {
        $this->duree = $duree;
        return $this;
    }

    public function getDuree()
    {
        return $this->duree;
    }
	

	
	
}
