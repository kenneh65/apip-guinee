<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PieceJointe
 *
 * @ORM\Table(name="piecejointe")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\PieceJointeRepository")
 */
class PieceJointe
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
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\TypeOperation")
     * @ORM\JoinColumn(name="idTypeOperation", referencedColumnName="id")
     */
    private $typeOperation;
	
	
    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\FormeJuridique")
     * @ORM\JoinColumn(name="idFormeJuridique", referencedColumnName="id")
     */
    private $formeJuridique;
    
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *  Get typeOperation
     *
     * @return \BanquemondialeBundle\Entity\TypeOperation
     */
    public function getTypeoperation()
    {
        return $this->typeOperation;
    }

    /**
     * Set typeOperation
     *
     * @param \BanquemondialeBundle\Entity\TypeOperation $typeOperation
     * @return PieceJointe
     */
    public function setTypeoperation($typeOperation)
    {
        $this->typeOperation = $typeOperation;
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
     *  Set fonction
     *
     * @param \BanquemondialeBundle\Entity\Fonction $fonction
     * @return PieceJointe
     */
    public function setFonction($fonction)
    {
        $this->fonction = $fonction;
    }    
   
    /**
     * Set formeJuridique
     *
     * @param \BanquemondialeBundle\Entity\FormeJuridique $formeJuridique
     * @return PieceJointe
     */
    public function setFormeJuridique(\BanquemondialeBundle\Entity\FormeJuridique $formeJuridique = null)
    {
        $this->formeJuridique = $formeJuridique;

        return $this;
    }

    /**
     * Get formeJuridique
     *
     * @return \BanquemondialeBundle\Entity\FormeJuridique 
     */
    public function getFormeJuridique()
    {
        return $this->formeJuridique;
    }

    /**
     * Set document
     *
     * @param \BanquemondialeBundle\Entity\Document $document
     * @return PieceJointe
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
	

}
