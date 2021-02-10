<?php

namespace ParametrageBundle\Entity\Formulaire;

use Symfony\Component\Validator\Constraints as Assert;

class FormulaireEi {

    /**
     * @var string
     * 
     * 
     */
    private $token;

    /**
     * @var string
     */
    private $numero_dossier;  /* -numeroDossier- */

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Le nom entreprise ne doit pas depasser 255 characteres"
     * )
     */
    private $nom_entreprise; /* -denominationSociale- */

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Le sigle ne doit pas depasser 255 characteres"
     * )     
     */
    private $sigle;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le type entreprise ne doit pas depasser 100 characteres"
     * )     
     */
    private $type_entreprise; /* qui est Personne Morale */

    /**
     * @var string
     *
     * 
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "L'email ne doit pas depasser 255 characteres"
     * )     
     */
    private $email;

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
    private $commune; 
    private $quartier;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "Le type rccm ne doit pas depasser 50 characteres"
     * )     
     */
    private $type_rccm; /* qui est GIE */

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
     * @var \DateTime
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Regex(pattern="/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",
     *     match=true,
     *     message="date rccm invalide")
     */
    private $date_rccm;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le type demande ne doit pas depasser 100 characteres"
     * )     
     */
    private $type_demande;
    private $renseignement_relatif_personne_physique;
    private $renseignement_relatif_activite_et_etablisement;
    private $autre_personnes_physique;

    /*     * ******************
     * getters setters 	*
     * ****************** */

    public function setNumeroDossier($numero_dossier) {
        $this->numero_dossier = $numero_dossier;
        return $this;
    }

    public function getNumeroDossier() {
        return $this->numero_dossier;
    }

    public function setNomEntreprise($nom_entreprise) {
        $this->nom_entreprise = $nom_entreprise;
        return $this;
    }

    public function getNomEntreprise() {
        return $this->nom_entreprise;
    }

    public function setSigle($sigle) {
        $this->sigle = $sigle;
        return $this;
    }

    public function getSigle() {
        return $this->sigle;
    }

    public function setTypeEntreprise($type_entreprise) {
        $this->type_entreprise = $type_entreprise;
        return $this;
    }

    public function getTypeEntreprise() {
        return $this->type_entreprise;
    }

    public function setTypeRccm($type_rccm) {
        $this->type_rccm = $type_rccm;
        return $this;
    }

    public function getTypeRccm() {
        return $this->type_rccm;
    }

    public function setRccm($rccm) {
        $this->rccm = $rccm;
        return $this;
    }

    public function getRccm() {
        return $this->rccm;
    }

    public function setDateRccm($date_rccm) {
        $this->date_rccm = $date_rccm;
        return $this;
    }

    public function getDateRccm() {
        return $this->date_rccm;
    }

    public function setTypeDemande($type_demande) {
        $this->type_demande = $type_demande;
        return $this;
    }

    public function getTypeDemande() {
        return $this->type_demande;
    }

    public function setPersPhysique($renseignement_relatif_personne_physique) {
        $this->renseignement_relatif_personne_physique = $renseignement_relatif_personne_physique;
    }

    public function getPersPhysique() {
        return $this->renseignement_relatif_personne_physique;
    }

    public function setActiviteEtablissement($renseignement_relatif_activite_et_etablisement) {
        $this->renseignement_relatif_activite_et_etablisement = $renseignement_relatif_activite_et_etablisement;
    }

    public function getActiviteEtablissement() {
        return $this->renseignement_relatif_activite_et_etablisement;
    }

    public function setAutresPersonnes(array $autre_personnes_physique) {
        $this->autre_personnes_physique = $autre_personnes_physique;
    }

    public function getAutresPersonnes() {
        return $this->autre_personnes_physique;
    }

    function getToken() {
        return $this->token;
    }

    function setToken($token) {
        $this->token = $token;
    }

    function getQuartier() {
        return $this->quartier;
    }

    function getCommune() {
        return $this->commune;
    }

    function setQuartier($quartier) {
        $this->quartier = $quartier;
    }

    function setCommune($commune) {
        $this->commune = $commune;
    }
    function getEmail() {
        return $this->email;
    }

    function getTelephone() {
        return $this->telephone;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setTelephone($telephone) {
        $this->telephone = $telephone;
    }


}
