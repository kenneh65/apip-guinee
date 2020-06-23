<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CollectionPieceJointe
 *
 * @ORM\Table(name="collectionpiecejointe")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\CollectionPieceJointeRepository")
 */
class CollectionPieceJointe
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateUpload", type="datetime")
     */
    private $dateUpload;

    /**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\DossierDemande")
      * @ORM\JoinColumn(name="idDossierDemande", referencedColumnName="id")
      */
    private $dossierDemande;
    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Document")
     * @ORM\JoinColumn(name="idDocument", referencedColumnName="id")
     */
    private $document;
    /**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Fonction")
      * @ORM\JoinColumn(name="idFonction", referencedColumnName="id",nullable=true)
      */
    private $fonction;
    /**
     * @var string
     *
     * @ORM\Column(name="pieceName", type="string", length=80)
     */
    private $pieceName;
	
	
	/**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Representant")
     * @ORM\JoinColumn(name="idRepresentant", referencedColumnName="id",nullable=true)
     */
    private $representant;   	



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
     * Set dateUpload
     *
     * @param \DateTime $dateUpload
     * @return CollectionPieceJointe
     */
    public function setDateUpload($dateUpload)
    {
        $this->dateUpload = $dateUpload;

        return $this;
    }

    /**
     * Get dateUpload
     *
     * @return \DateTime 
     */
    public function getDateUpload()
    {
        return $this->dateUpload;
    }

    /**
     * Set pieceName
     *
     * @param string $pieceName
     * @return CollectionPieceJointe
     */
    public function setPieceName($pieceName)
    {
        $this->pieceName = $pieceName;

        return $this;
    }

    /**
     * Get pieceName
     *
     * @return string 
     */
    public function getPieceName()
    {
        return $this->pieceName;
    }

    /**
     * Set dossierDemande
     *
     * @param \BanquemondialeBundle\Entity\DossierDemande $dossierDemande
     * @return CollectionPieceJointe
     */
    public function setDossierDemande(\BanquemondialeBundle\Entity\DossierDemande $dossierDemande = null)
    {
        $this->dossierDemande = $dossierDemande;

        return $this;
    }

    /**
     * Get dossierDemande
     *
     * @return \BanquemondialeBundle\Entity\DossierDemande 
     */
    public function getDossierDemande()
    {
        return $this->dossierDemande;
    }

    /**
     * Set document
     *
     * @param \BanquemondialeBundle\Entity\Document $document
     * @return CollectionPieceJointe
     */
    public function setDocument(\BanquemondialeBundle\Entity\Document $document = null)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \BanquemondialeBundle\Entity\Document 
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set fonction
     *
     * @param \BanquemondialeBundle\Entity\Fonction $fonction
     * @return CollectionPieceJointe
     */
    public function setFonction(\BanquemondialeBundle\Entity\Fonction $fonction = null)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get fonction
     *
     * @return \BanquemondialeBundle\Entity\Fonction 
     */
    public function getFonction()
    {
        return $this->fonction;
    }
	
		/**
     * Get representant
     *
     * @return \BanquemondialeBundle\Entity\Representant
     */
    public function getRepresentant()
    {
        return $this->representant;
    }

    /**
     *  Set representant
     *
     * @param \BanquemondialeBundle\Entity\Representant $representant
     * @return PieceJointe
     */
    public function setRepresentant($representant)
    {
        $this->representant = $representant;
    }    
}
