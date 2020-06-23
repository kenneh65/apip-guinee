<?php

namespace ParametrageBundle\Entity\Formulaire;

use Symfony\Component\Validator\Constraints as Assert;

class RenseignementRelatifGroupementFormulaire
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
     *      max = 50,
     *      maxMessage = "Le sigle ne doit pas depasser 50 characteres"
     * )     
     */
    private $sigle;
	
	

	
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
     *      maxMessage = "La forme juridique ne doit pas depasser 255 characteres"
     * )
     */
    private $forme_juridique;
	
	
	/**
     * @var integer
     */    
    private $capital_social;
	
	
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
	
	
	public function setSigle($sigle)
    {
        $this->sigle = $sigle;
        return $this;
    }

    public function getSigle()
    {
        return $this->sigle;
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
	
	
	public function setFormeJuridique($forme_juridique)
    {
        $this->forme_juridique = $forme_juridique;
        return $this;
    }

    public function getFormeJuridique()
    {
        return $this->forme_juridique;
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
