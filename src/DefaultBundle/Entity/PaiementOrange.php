<?php

namespace DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaiementOrange
 *
 * @ORM\Table(name="paiement_orange")
 * @ORM\Entity(repositoryClass="DefaultBundle\Repository\PaiementOrangeRepository")
 */
class PaiementOrange
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
     * @ORM\OneToMany(targetEntity="DefaultBundle\Entity\factureOrange", mappedBy="PaiementOrange")
     *  @ORM\JoinColumn(nullable=true)
     */
    private $factureOrange;

    /**
     * @var string
     *
     * @ORM\Column(name="orderId", type="string", length=255, nullable=true, unique=true)
     */
    private $orderId;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var array
     *
     * @ORM\Column(name="customer", type="array", nullable=true)
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="payToken", type="string", length=255, nullable=true)
     */
    private $payToken;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=50, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="txnid", type="string", length=255, nullable=true)
     */
    private $txnid;


    /**
     * @ORM\ManyToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


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
     * Set orderId
     *
     * @param string $orderId
     * @return PaiementOrange
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return string 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     * @return PaiementOrange
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set customer
     *
     * @param array $customer
     * @return PaiementOrange
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return array 
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set payToken
     *
     * @param string $payToken
     * @return PaiementOrange
     */
    public function setPayToken($payToken)
    {
        $this->payToken = $payToken;

        return $this;
    }

    /**
     * Get payToken
     *
     * @return string 
     */
    public function getPayToken()
    {
        return $this->payToken;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return PaiementOrange
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set txnid
     *
     * @param string $txnid
     * @return PaiementOrange
     */
    public function setTxnid($txnid)
    {
        $this->txnid = $txnid;

        return $this;
    }

    /**
     * Get txnid
     *
     * @return string 
     */
    public function getTxnid()
    {
        return $this->txnid;
    }


    /**
     * Set user
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $user
     * @return PaiementOrange
     */
    public function setUser(\UtilisateursBundle\Entity\Utilisateurs $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UtilisateursBundle\Entity\Utilisateurs 
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->factureOrange = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add factureOrange
     *
     * @param \DefaultBundle\Entity\factureOrange $factureOrange
     * @return PaiementOrange
     */
    public function addFactureOrange(\DefaultBundle\Entity\factureOrange $factureOrange)
    {
        $this->factureOrange[] = $factureOrange;

        return $this;
    }

    /**
     * Remove factureOrange
     *
     * @param \DefaultBundle\Entity\factureOrange $factureOrange
     */
    public function removeFactureOrange(\DefaultBundle\Entity\factureOrange $factureOrange)
    {
        $this->factureOrange->removeElement($factureOrange);
    }

    /**
     * Get factureOrange
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFactureOrange()
    {
        return $this->factureOrange;
    }
}