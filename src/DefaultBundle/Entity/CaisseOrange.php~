<?php

namespace DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CaisseOrange
 *
 * @ORM\Table(name="caisse_orange")
 * @ORM\Entity(repositoryClass="DefaultBundle\Repository\CaisseOrangeRepository")
 */
class CaisseOrange
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
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="totalAmount", type="float", nullable=true)
     */
    private $totalAmount;


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
     * Set amount
     *
     * @param float $amount
     * @return CaisseOrange
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
     * Set totalAmount
     *
     * @param float $totalAmount
     * @return CaisseOrange
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * Get totalAmount
     *
     * @return float 
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }
}
