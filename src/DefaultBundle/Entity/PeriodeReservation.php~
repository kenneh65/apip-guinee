<?php

namespace DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * PeriodeReservation
 *
 * @ORM\Table(name="periode_reservation")
 * @ORM\Entity(repositoryClass="DefaultBundle\Repository\PeriodeReservationRepository")
 *  @ORM\Table(uniqueConstraints={@UniqueConstraint(name="formeJuridique_periode_unique",columns={"iFormeJuridiqueTraduction","libelle"})})
 */
class PeriodeReservation
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
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\FormeJuridiqueTraduction",cascade={"persist"}, inversedBy="periodeReservation")
     * @ORM\JoinColumn(name="iFormeJuridiqueTraduction", referencedColumnName="id")
     */
    private $formeJuridiqueTraduction;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column(name="nombre", type="integer")
     */
    private $nombre;


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
     * Set libelle
     *
     * @param string $libelle
     * @return PeriodeReservation
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return PeriodeReservation
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set nombre
     *
     * @param integer $nombre
     * @return PeriodeReservation
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return integer 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set formeJuridiqueTraduction
     *
     * @param \BanquemondialeBundle\Entity\FormeJuridiqueTraduction $formeJuridiqueTraduction
     * @return PeriodeReservation
     */
    public function setFormeJuridiqueTraduction(\BanquemondialeBundle\Entity\FormeJuridiqueTraduction $formeJuridiqueTraduction = null)
    {
        $this->formeJuridiqueTraduction = $formeJuridiqueTraduction;

        return $this;
    }

    /**
     * Get formeJuridiqueTraduction
     *
     * @return \BanquemondialeBundle\Entity\FormeJuridiqueTraduction 
     */
    public function getFormeJuridiqueTraduction()
    {
        return $this->formeJuridiqueTraduction;
    }
}
