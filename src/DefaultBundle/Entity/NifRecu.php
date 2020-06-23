<?php

namespace DefaultBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NifRecu
 *
 */
class NifRecu {

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $code;
    
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $numeroDossier;

    /**
     * 
     * @var string
     */
    private $numeroIdentificationFiscale;

    /**
     * 
     * @var datetime
     */
    private $dateImmatriculation;

    /**
     * 
     * @var string
     */
    private $numeroFormulaire;

    /**
     * @var string
     */
    private $nomPrepose;

    /**
     * @var string
     */
    private $prenomPrepose;

    /**
     * 
     * @var string
     */
    private $nomFichierEnvoye;

    /**
     * @var string
     *
     */
    private $secteur;

    /**
     * @var string
     *
     * 
     */
    private $rue;

    /**
     * @var string
     *
     * 
     */
    private $marche;

    /**
     * @var string
     *
     */
    private $boutique;
    /**
     * @var array
     * 
     */
   private $messages;

    /**
     * Set numeroDossier
     *
     * @param string $numeroDossier
     * @return NifRecu
     */
    public function setNumeroDossier($numeroDossier) {
        $this->numeroDossier = $numeroDossier;

        return $this;
    }

    /**
     * Get numeroFormulaire
     *
     * @return string 
     */
    public function getNumeroDossier() {
        return $this->numeroDossier;
    }

    /**
     * Set numeroFormulaire
     *
     * @param string $numeroFormulaire
     * @return NifRecu
     */
    public function setNumeroFormulaire($numeroFormulaire) {
        $this->numeroFormulaire = $numeroFormulaire;

        return $this;
    }

    /**
     * Get numeroFormulaire
     *
     * @return string 
     */
    public function getNumeroFormulaire() {
        return $this->numeroFormulaire;
    }

    /**
     * Set numeroIdentificationFiscale
     *
     * @param string $numeroNif
     * @return NifRecu
     */
    public function setNumeroIdentificationFiscale($numeroNif) {
        $this->numeroIdentificationFiscale = $numeroNif;

        return $this;
    }

    /**
     * Get numeroIdentificationFiscale
     *
     * @return string 
     */
    public function getNumeroIdentificationFiscale() {
        return $this->numeroIdentificationFiscale;
    }

    /**
     * Set nomPrepose
     *
     * @param string $nomPrepose
     * @return NifRecu
     */
    public function setNomPrepose($nomPrepose) {
        $this->nomPrepose = $nomPrepose;

        return $this;
    }

    /**
     * Get nomPrepose
     *
     * @return string 
     */
    public function getNomPrepose() {
        return $this->nomPrepose;
    }

    /**
     * Set prenomPrepose
     *
     * @param string $prenomPrepose
     * @return NifRecu
     */
    public function setPrenomPrepose($prenomPrepose) {
        $this->prenomPrepose = $prenomPrepose;

        return $this;
    }

    /**
     * Get prenomPrepose
     *
     * @return string 
     */
    public function getPrenomPrepose() {
        return $this->prenomPrepose;
    }

    /**
     * Set nomFichierEnvoye
     *
     * @param string $nomFichier
     * @return NifRecu
     */
    public function setNomFichierEnvoye($nomFichier) {
        $this->nomFichierEnvoye = $nomFichier;

        return $this;
    }

    /**
     * Get nomFichierEnvoye
     *
     * @return string 
     */
    public function getNomFichierEnvoye() {
        return $this->nomFichierEnvoye;
    }

    /**
     * Set dateImmatriculation
     *
     * @param datetime $dateImmatriculation
     * @return NifRecu
     */
    public function setDateImmatriculation($dateImmatriculation) {
        $this->dateImmatriculation = $dateImmatriculation;

        return $this;
    }

    /**
     * Get dateImmaticulation
     *
     * @return string 
     */
    public function getDateImmatriculation() {
        return $this->dateImmatriculation;
    }
    
    function getSecteur() {
        return $this->secteur;
    }

    function getRue() {
        return $this->rue;
    }

    function getMarche() {
        return $this->marche;
    }

    function getBoutique() {
        return $this->boutique;
    }

    function setSecteur($secteur) {
        $this->secteur = $secteur;
    }

    function setRue($rue) {
        $this->rue = $rue;
    }

    function setMarche($marche) {
        $this->marche = $marche;
    }

    function setBoutique($boutique) {
        $this->boutique = $boutique;
    }

    function getCode() {
        return $this->code;
    }

    function setCode($code) {
        $this->code = $code;
    }


    function getMessages() {
        return $this->messages;
    }

    function setMessages($messages) {
        $this->messages = $messages;
    }


}
