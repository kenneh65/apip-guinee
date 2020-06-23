<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArchiveNomCommerciaux
 *
 * @ORM\Table(name="archivenomcommerciaux")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\ArchiveNomCommerciauxRepository")
 */
class ArchiveNomCommerciaux
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
     * @var string
     *
     * @ORM\Column(name="denominationSociale", type="string", length=255, unique=false, nullable=true)
     */
    private $denominationSociale;

    /**
     * @var string
     *
     * @ORM\Column(name="formeJuridique", type="string", length=60, nullable=true)
     */
    private $formeJuridique;
	
	/**
     * @var string
     *
     * @ORM\Column(name="rccm", type="string", length=30, nullable=true)
     */
    private $rccm;

    /**
     * @var string
     *
     * @ORM\Column(name="gerantPrincipal", type="string", length=255, nullable=true)
     */
    private $gerantPrincipal;

    /**
     * @var string
     *
     * @ORM\Column(name="secteurActivite", type="string", length=100, nullable=true)
     */
    private $secteurActivite;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=30, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="siegeSocial", type="string", length=255, nullable=true)
     */
    private $siegeSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="anneeCreation", type="string", length=10, nullable=true)
     */
    private $anneeCreation;


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
     * Set denominationSociale
     *
     * @param string $denominationSociale
     * @return ArchiveNomCommerciaux
     */
    public function setDenominationSociale($denominationSociale)
    {
        $this->denominationSociale = $denominationSociale;

        return $this;
    }

    /**
     * Get denominationSociale
     *
     * @return string 
     */
    public function getDenominationSociale()
    {
        return $this->denominationSociale;
    }

    /**
     * Set formeJuridique
     *
     * @param string $formeJuridique
     * @return ArchiveNomCommerciaux
     */
    public function setFormeJuridique($formeJuridique)
    {
        $this->formeJuridique = $formeJuridique;

        return $this;
    }

    /**
     * Get formeJuridique
     *
     * @return string 
     */
    public function getFormeJuridique()
    {
        return $this->formeJuridique;
    }
	
	
	/**
     * Set rccm
     *
     * @param string $rccm
     * @return ArchiveNomCommerciaux
     */
    public function setRccm($rccm)
    {
        $this->rccm = $rccm;

        return $this;
    }

    /**
     * Get rccm
     *
     * @return string 
     */
    public function getRccm()
    {
        return $this->rccm;
    }
	

    /**
     * Set gerantPrincipal
     *
     * @param string $gerantPrincipal
     * @return ArchiveNomCommerciaux
     */
    public function setGerantPrincipal($gerantPrincipal)
    {
        $this->gerantPrincipal = $gerantPrincipal;

        return $this;
    }

    /**
     * Get gerantPrincipal
     *
     * @return string 
     */
    public function getGerantPrincipal()
    {
        return $this->gerantPrincipal;
    }

    /**
     * Set secteurActivite
     *
     * @param string $secteurActivite
     * @return ArchiveNomCommerciaux
     */
    public function setSecteurActivite($secteurActivite)
    {
        $this->secteurActivite = $secteurActivite;

        return $this;
    }

    /**
     * Get secteurActivite
     *
     * @return string 
     */
    public function getSecteurActivite()
    {
        return $this->secteurActivite;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return ArchiveNomCommerciaux
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
     * Set siegeSocial
     *
     * @param string $siegeSocial
     * @return ArchiveNomCommerciaux
     */
    public function setSiegeSocial($siegeSocial)
    {
        $this->siegeSocial = $siegeSocial;

        return $this;
    }

    /**
     * Get siegeSocial
     *
     * @return string 
     */
    public function getSiegeSocial()
    {
        return $this->siegeSocial;
    }

    /**
     * Set anneeCreation
     *
     * @param string $anneeCreation
     * @return ArchiveNomCommerciaux
     */
    public function setAnneeCreation($anneeCreation)
    {
        $this->anneeCreation = $anneeCreation;

        return $this;
    }

    /**
     * Get anneeCreation
     *
     * @return string 
     */
    public function getAnneeCreation()
    {
        return $this->anneeCreation;
    }
}
