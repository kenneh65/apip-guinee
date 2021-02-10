<?php

namespace DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="reglageactivation")
 * @ORM\Entity(repositoryClass="DefaultBundle\Repository\ReglageActivationRepository")
 */
class ReglageActivation {

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
     * @ORM\Column(name="libelleSignatureGreffe", type="string", length=100)
     */
    private $libelleSignatureGreffe;

    /**
     * @var bool
     *
     * @ORM\Column(name="isQRVisible", type="boolean")
     */
    private $isQRVisible;
	
	/**
     * @var bool
     *
     * @ORM\Column(name="isSignatureVisible", type="boolean")
     */
    private $isSignatureVisible;

    
	

    /**
     * Constructor
     */
    public function __construct() {
        $this->setIsQRVisible(true);
		$this->getIsSignatureVisible(true);
		$this->setLibelleSignatureGreffe("Me Alseny Fofana Greffier en chef du TPI de Kaloum");
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
     * Set libelleSignatureGreffe
     *
     * @param string $libelleSignatureGreffe
     * @return ReglageActivation
     */
    public function setLibelleSignatureGreffe($libelleSignatureGreffe)
    {
        $this->libelleSignatureGreffe = $libelleSignatureGreffe;

        return $this;
    }

    /**
     * Get libelleSignatureGreffe
     *
     * @return string 
     */
    public function getLibelleSignatureGreffe()
    {
        return $this->libelleSignatureGreffe;
    }

    /**
     * Set isQRVisible
     *
     * @param boolean $isQRVisible
     * @return ReglageActivation
     */
    public function setIsQRVisible($isQRVisible)
    {
        $this->isQRVisible = $isQRVisible;

        return $this;
    }

    /**
     * Get isQRVisible
     *
     * @return boolean 
     */
    public function getIsQRVisible()
    {
        return $this->isQRVisible;
    }

    /**
     * Set isSignatureVisible
     *
     * @param boolean $isSignatureVisible
     * @return ReglageActivation
     */
    public function setIsSignatureVisible($isSignatureVisible)
    {
        $this->isSignatureVisible = $isSignatureVisible;

        return $this;
    }

    /**
     * Get isSignatureVisible
     *
     * @return boolean 
     */
    public function getIsSignatureVisible()
    {
        return $this->isSignatureVisible;
    }
}
