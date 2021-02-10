<?php

namespace DefaultBundle\Entity;

use DefaultBundle\services\monServices;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * reservation
 *
 * @ORM\Table(name="reservation")
 * @ORM\Entity(repositoryClass="DefaultBundle\Repository\reservationRepository")
 */
class reservation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="statut", type="boolean", nullable=true)
     */
    private $statut;

    /**
     * @var string
     *
     * @ORM\Column(name="nomCommercial", type="string", length=255, unique=false)
     */
    private $nomCommercial;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="validationCode", type="string", length=255,nullable=true)
     */
    private $validationCode;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="modePaiement", type="string", length=255,nullable=true)
     */
    private $modePaiement;


    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255)
     */
    private $adresse;


    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255)
     */
    private $telephone;

    /**
 * @var datetime
 *
 * @ORM\Column(name="dateCreation", type="datetime",nullable=true)
 */
    private $dateCreation;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="datetime", nullable=true)
     */
    private $dateFin;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="datetime",nullable=true)
     */
    private $dateDebut;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\FormeJuridiqueTraduction")
     * @ORM\JoinColumn(name="iFormeJuridiqueTraduction", referencedColumnName="id")
     */
    private $formeJuridique;

    /**
     * @ORM\OneToMany(targetEntity="DefaultBundle\Entity\detailReservation", mappedBy="reservation")
     */
    private $detailReservation;


    public  function __construct()
    {
        $this->detailReservation = new ArrayCollection();
        $this->dateCreation=new \DateTime();
        $this->setStatut(true);
        $code=new monServices();
        $this->setValidationCode($code->generateCustomToken(11111111,99999999));
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nomCommercial
     *
     * @param string $nomCommercial
     * @return reservation
     */
    public function setNomCommercial($nomCommercial)
    {
        $this->nomCommercial = $nomCommercial;

        return $this;
    }

    /**
     * Get nomCommercial
     *
     * @return string 
     */
    public function getNomCommercial()
    {
        return $this->nomCommercial;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return reservation
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return reservation
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return reservation
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string 
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return reservation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return reservation
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return reservation
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }


    /**
     * Set formeJuridique
     *
     * @param \BanquemondialeBundle\Entity\FormeJuridiqueTraduction $formeJuridique
     * @return reservation
     */
    public function setFormeJuridique(\BanquemondialeBundle\Entity\FormeJuridiqueTraduction $formeJuridique = null)
    {
        $this->formeJuridique = $formeJuridique;

        return $this;
    }

    /**
     * Get formeJuridique
     *
     * @return \BanquemondialeBundle\Entity\FormeJuridiqueTraduction 
     */
    public function getFormeJuridique()
    {
        return $this->formeJuridique;
    }

    /**
     * Set modePaiement
     *
     * @param string $modePaiement
     * @return reservation
     */
    public function setModePaiement($modePaiement)
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    /**
     * Get modePaiement
     *
     * @return string 
     */
    public function getModePaiement()
    {
        return $this->modePaiement;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     * @return reservation
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime 
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return reservation
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime 
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Add detailReservation
     *
     * @param \DefaultBundle\Entity\detailReservation $detailReservation
     * @return reservation
     */
    public function addDetailReservation(\DefaultBundle\Entity\detailReservation $detailReservation)
    {
        $this->detailReservation[] = $detailReservation;

        return $this;
    }

    /**
     * Remove detailReservation
     *
     * @param \DefaultBundle\Entity\detailReservation $detailReservation
     */
    public function removeDetailReservation(\DefaultBundle\Entity\detailReservation $detailReservation)
    {
        $this->detailReservation->removeElement($detailReservation);
    }

    /**
     * Get detailReservation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDetailReservation()
    {
        return $this->detailReservation;
    }

    /**
     * Set validationCode
     *
     * @param string $validationCode
     * @return reservation
     */
    public function setValidationCode($validationCode)
    {
        $this->validationCode = $validationCode;

        return $this;
    }

    /**
     * Get validationCode
     *
     * @return string 
     */
    public function getValidationCode()
    {
        return $this->validationCode;
    }

    /**
     * Set statut
     *
     * @param boolean $statut
     * @return reservation
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return boolean 
     */
    public function getStatut()
    {
        return $this->statut;
    }
}
