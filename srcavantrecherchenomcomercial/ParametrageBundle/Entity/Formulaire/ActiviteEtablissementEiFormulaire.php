<?php

namespace ParametrageBundle\Entity\Formulaire;

use Symfony\Component\Validator\Constraints as Assert;

class ActiviteEtablissementEiFormulaire {

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le nom entreprise ne doit pas depasser 100 characteres"
     * )
     */
    private $nom_commercial; /* -denominationSociale- */

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
    private $groupe_activite_exercee;
    private $activite_exercee;
    private $groupe_activite_secondaire_1;
    private $activite_secondaire_1;
    private $groupe_activite_secondaire_2;
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
     * @var string
     *
     * @Assert\Regex(pattern="/^[a-zA-Z]{2}\.TCC\.[0-9]{4}\.[a-zA-Z]{1}\.[0-9 ]+$/",
     *     match=true,
     *     message="format rccm invalide")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le rccm ne doit pas depasser 100 characteres"
     * )
     */
    private $rccm;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *      max = 250,
     *      maxMessage = "L'adresse ne doit pas depasser 250 characteres"
     * )     
     */
    private $adresse_principale;

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
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le nom commercial de l'ets secondaire ne doit pas depasser 100 characteres"
     * )     
     */
    private $nom_commercial_ets_second_ou_succ;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "Le sigle de l'ets secondaire ne doit pas depasser 50 characteres"
     * )     
     */
    private $sigle_ets_second_ou_succ;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Regex(pattern="/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",
     *     match=true,
     *     message="date debut invalide")
     */
    private $date_ouverture_ets_second_ou_succ;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *      max = 250,
     *      maxMessage = "L'adresse de l'ets secondaire ne doit pas depasser 250 characteres"
     * )     
     */
    private $adresse_ets_second_ou_succ;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *      max = 250,
     *      maxMessage = "L'activite de l'ets secondaire ne doit pas depasser 250 characteres"
     * )     
     */
    private $activite_ets_second_ou_succ;

    /*     * ******************
     * getters setters 	*
     * ****************** */

    public function setGroupeActivitePrincipale($groupe_activite_exercee) {
        $this->groupe_activite_exercee = $groupe_activite_exercee;
        return $this;
    }

    public function getGroupeActivitePrincipale() {
        return $this->groupe_activite_exercee;
    }

    public function setGroupeActiviteSecondaire1($groupe_activite_secondaire_1) {
        $this->groupe_activite_secondaire_1 = $groupe_activite_secondaire_1;
        return $this;
    }

    public function getGroupeActiviteSecondaire1() {
        return $this->groupe_activite_secondaire_1;
    }

    public function setGroupeActiviteSecondaire2($groupe_activite_secondaire_2) {
        $this->groupe_activite_secondaire_2 = $groupe_activite_secondaire_2;
        return $this;
    }

    public function getGroupeActiviteSecondaire2() {
        return $this->groupe_activite_secondaire_2;
    }

    public function setNomCommercial($nom_commercial) {
        $this->nom_commercial = $nom_commercial;
        return $this;
    }

    public function getNomCommercial() {
        return $this->nom_commercial;
    }

    public function setSigle($sigle) {
        $this->sigle = $sigle;
        return $this;
    }

    public function getSigle() {
        return $this->sigle;
    }

    public function setActiviteExercee($activite_exercee) {
        $this->activite_exercee = $activite_exercee;
        return $this;
    }

    public function getActiviteExercee() {
        return $this->activite_exercee;
    }

    public function setActiviteSecondaire1($activite_secondaire_1) {
        $this->activite_secondaire_1 = $activite_secondaire_1;
        return $this;
    }

    public function getActiviteSecondaire1() {
        return $this->activite_secondaire_1;
    }

    public function setActiviteSecondaire2($activite_secondaire_2) {
        $this->activite_secondaire_2 = $activite_secondaire_2;
        return $this;
    }

    public function getActiviteSecondaire2() {
        return $this->activite_secondaire_2;
    }

    public function setDateDebut($date_debut) {
        $this->date_debut = $date_debut;
        return $this;
    }

    public function getDateDebut() {
        return $this->date_debut;
    }

    public function setRccm($rccm) {
        $this->rccm = $rccm;
        return $this;
    }

    public function getRccm() {
        return $this->rccm;
    }

    public function setAdressePrincipale($adresse_principale) {
        $this->adresse_principale = $adresse_principale;

        return $this;
    }

    public function getAdressePrincipale() {
        return $this->adresse_principale;
    }

    public function setOrigine($origine) {
        $this->origine = $origine;
        return $this;
    }

    public function getOrigine() {
        return $this->origine;
    }

    public function setNomCommercialSucc($nom_commercial_ets_second_ou_succ) {
        $this->nom_commercial_ets_second_ou_succ = $nom_commercial_ets_second_ou_succ;
        return $this;
    }

    public function getNomCommercialSucc() {
        return $this->nom_commercial_ets_second_ou_succ;
    }

    public function setSigleSucc($sigle_ets_second_ou_succ) {
        $this->sigle_ets_second_ou_succ = $sigle_ets_second_ou_succ;
        return $this;
    }

    public function getSigleSucc() {
        return $this->sigle_ets_second_ou_succ;
    }

    public function setDateOuvertureSucc($date_ouverture_ets_second_ou_succ) {
        $this->date_ouverture_ets_second_ou_succ = $date_ouverture_ets_second_ou_succ;
        return $this;
    }

    public function getDateOuvertureSucc() {
        return $this->date_ouverture_ets_second_ou_succ;
    }

    public function setAdresseSucc($adresse_ets_second_ou_succ) {
        $this->adresse_ets_second_ou_succ = $adresse_ets_second_ou_succ;
        return $this;
    }

    public function getAdresseSucc() {
        return $this->adresse_ets_second_ou_succ;
    }

    public function setActiviteSucc($activite_ets_second_ou_succ) {
        $this->activite_ets_second_ou_succ = $activite_ets_second_ou_succ;
        return $this;
    }

    public function getActiviteSucc() {
        return $this->activite_ets_second_ou_succ;
    }

}
