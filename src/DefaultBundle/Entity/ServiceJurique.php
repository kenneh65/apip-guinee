<?php

namespace DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceJurique
 *
 * @ORM\Table(name="service_jurique")
 * @ORM\Entity(repositoryClass="DefaultBundle\Repository\ServiceJuriqueRepository")
 */
class ServiceJurique
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
     * @ORM\ManyToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs")
     * @ORM\JoinColumn(name="idUtilisateur", referencedColumnName="id")
     */
    private $utilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="denominationCommercial", type="string", length=255, unique=true)
     */
    private $denominationCommercial;

    /**
     * @var bool
     *
     * @ORM\Column(name="isPiecesIdentite", type="boolean")
     */
    private $isPiecesIdentite;

    /**
     * @var bool
     *
     * @ORM\Column(name="isDenomination", type="boolean")
     */
    private $isDenomination;

    /**
     * @var bool
     *
     * @ORM\Column(name="isConformiteJuridique", type="boolean")
     */
    private $isConformiteJuridique;

    /**
     * @var bool
     *
     * @ORM\Column(name="isCapital", type="boolean")
     */
    private $isCapital;

    /**
     * @var bool
     *
     * @ORM\Column(name="isDuree", type="boolean")
     */
    private $isDuree;

    /**
     * @var bool
     *
     * @ORM\Column(name="isActivites", type="boolean")
     */
    private $isActivites;

    /**
     * @var string
     *
     * @ORM\Column(name="remarqueVerificateur", type="string", length=255, nullable=true)
     */
    private $remarqueVerificateur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateVerification", type="datetimetz")
     */
    private $dateVerification;

    /**
     * @var bool
     *
     * @ORM\Column(name="isCreated", type="boolean", nullable=true)
     */
    private $isCreated;


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
     * Set denominationCommercial
     *
     * @param string $denominationCommercial
     * @return ServiceJurique
     */
    public function setDenominationCommercial($denominationCommercial)
    {
        $this->denominationCommercial = $denominationCommercial;

        return $this;
    }

    /**
     * Get denominationCommercial
     *
     * @return string 
     */
    public function getDenominationCommercial()
    {
        return $this->denominationCommercial;
    }

    /**
     * Set isPiecesIdentite
     *
     * @param boolean $isPiecesIdentite
     * @return ServiceJurique
     */
    public function setIsPiecesIdentite($isPiecesIdentite)
    {
        $this->isPiecesIdentite = $isPiecesIdentite;

        return $this;
    }

    /**
     * Get isPiecesIdentite
     *
     * @return boolean 
     */
    public function getIsPiecesIdentite()
    {
        return $this->isPiecesIdentite;
    }

    /**
     * Set isDenomination
     *
     * @param boolean $isDenomination
     * @return ServiceJurique
     */
    public function setIsDenomination($isDenomination)
    {
        $this->isDenomination = $isDenomination;

        return $this;
    }

    /**
     * Get isDenomination
     *
     * @return boolean 
     */
    public function getIsDenomination()
    {
        return $this->isDenomination;
    }

    /**
     * Set isConformiteJuridique
     *
     * @param boolean $isConformiteJuridique
     * @return ServiceJurique
     */
    public function setIsConformiteJuridique($isConformiteJuridique)
    {
        $this->isConformiteJuridique = $isConformiteJuridique;

        return $this;
    }

    /**
     * Get isConformiteJuridique
     *
     * @return boolean 
     */
    public function getIsConformiteJuridique()
    {
        return $this->isConformiteJuridique;
    }

    /**
     * Set isCapital
     *
     * @param boolean $isCapital
     * @return ServiceJurique
     */
    public function setIsCapital($isCapital)
    {
        $this->isCapital = $isCapital;

        return $this;
    }

    /**
     * Get isCapital
     *
     * @return boolean 
     */
    public function getIsCapital()
    {
        return $this->isCapital;
    }

    /**
     * Set isDuree
     *
     * @param boolean $isDuree
     * @return ServiceJurique
     */
    public function setIsDuree($isDuree)
    {
        $this->isDuree = $isDuree;

        return $this;
    }

    /**
     * Get isDuree
     *
     * @return boolean 
     */
    public function getIsDuree()
    {
        return $this->isDuree;
    }

    /**
     * Set isActivites
     *
     * @param boolean $isActivites
     * @return ServiceJurique
     */
    public function setIsActivites($isActivites)
    {
        $this->isActivites = $isActivites;

        return $this;
    }

    /**
     * Get isActivites
     *
     * @return boolean 
     */
    public function getIsActivites()
    {
        return $this->isActivites;
    }

    /**
     * Set remarqueVerificateur
     *
     * @param string $remarqueVerificateur
     * @return ServiceJurique
     */
    public function setRemarqueVerificateur($remarqueVerificateur)
    {
        $this->remarqueVerificateur = $remarqueVerificateur;

        return $this;
    }

    /**
     * Get remarqueVerificateur
     *
     * @return string 
     */
    public function getRemarqueVerificateur()
    {
        return $this->remarqueVerificateur;
    }

    /**
     * Set dateVerification
     *
     * @param \DateTime $dateVerification
     * @return ServiceJurique
     */
    public function setDateVerification($dateVerification)
    {
        $this->dateVerification = $dateVerification;

        return $this;
    }

    /**
     * Get dateVerification
     *
     * @return \DateTime 
     */
    public function getDateVerification()
    {
        return $this->dateVerification;
    }

    /**
     * Set isCreated
     *
     * @param boolean $isCreated
     * @return ServiceJurique
     */
    public function setIsCreated($isCreated)
    {
        $this->isCreated = $isCreated;

        return $this;
    }

    /**
     * Get isCreated
     *
     * @return boolean 
     */
    public function getIsCreated()
    {
        return $this->isCreated;
    }

    /**
     * Set utilisateur
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $utilisateur
     * @return ServiceJurique
     */
    public function setUtilisateur(\UtilisateursBundle\Entity\Utilisateurs $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \UtilisateursBundle\Entity\Utilisateurs 
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }
}
