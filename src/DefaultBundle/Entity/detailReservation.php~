<?php

namespace DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * detailReservation
 *
 * @ORM\Table(name="detail_reservation")
 * @ORM\Entity(repositoryClass="DefaultBundle\Repository\detailReservationRepository")
 */
class detailReservation
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
     * @ORM\ManyToOne(targetEntity="DefaultBundle\Entity\reservation", inversedBy="detailReservation")
     * @ORM\JoinColumn(name="idReservation", referencedColumnName="id")
     */
    private $reservation;


    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\FormeJuridiqueTraduction")
     * @ORM\JoinColumn(name="iFormeJuridiqueTraduction", referencedColumnName="id",nullable=true)
     */
    private $formeJuridiqueTraduction;


    /**
     * @ORM\ManyToOne(targetEntity="DefaultBundle\Entity\PeriodeReservation")
     * @ORM\JoinColumn(name="iPeriodeReservation", referencedColumnName="id",nullable=true)
     */
    private $PeriodeReservation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="datetimetz")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="datetimetz", nullable=true)
     */
    private $dateFin;

    /**
     * @var bool
     *
     * @ORM\Column(name="statut", type="boolean", nullable=true)
     */
    private $statut;


    /**
     * @var bool
     *
     * @ORM\Column(name="isPaid", type="boolean", nullable=true)
     */
    private $isPaid;

    /**
     * @var array
     *
     * @ORM\Column(name="operation", type="array", nullable=true)
     */
    private $operation;
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
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return detailReservation
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
     * Set dateFin
     *
     * @param \DateTime $dateFin
     * @return detailReservation
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
     * Set statut
     *
     * @param boolean $statut
     * @return detailReservation
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

    /**
     * Set operation
     *
     * @param array $operation
     * @return detailReservation
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation
     *
     * @return array 
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set reservation
     *
     * @param \DefaultBundle\Entity\reservation $reservation
     * @return detailReservation
     */
    public function setReservation(\DefaultBundle\Entity\reservation $reservation = null)
    {
        $this->reservation = $reservation;
        return $this;
    }

    /**
     * Get reservation
     *
     * @return \DefaultBundle\Entity\reservation 
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * Set isPaid
     *
     * @param boolean $isPaid
     * @return detailReservation
     */
    public function setIsPaid($isPaid)
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    /**
     * Get isPaid
     *
     * @return boolean 
     */
    public function getIsPaid()
    {
        return $this->isPaid;
    }

    /**
     * Set formeJuridiqueTraduction
     *
     * @param \BanquemondialeBundle\Entity\FormeJuridiqueTraduction $formeJuridiqueTraduction
     * @return detailReservation
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

    /**
     * Set PeriodeReservation
     *
     * @param \DefaultBundle\Entity\PeriodeReservation $periodeReservation
     * @return detailReservation
     */
    public function setPeriodeReservation(\DefaultBundle\Entity\PeriodeReservation $periodeReservation = null)
    {
        $this->PeriodeReservation = $periodeReservation;

        return $this;
    }

    /**
     * Get PeriodeReservation
     *
     * @return \DefaultBundle\Entity\PeriodeReservation 
     */
    public function getPeriodeReservation()
    {
        return $this->PeriodeReservation;
    }
}
