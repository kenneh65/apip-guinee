<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentTraduction
 *
 * @ORM\Table(name="documenttraduction")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\DocumentTraductionRepository")
 */
class DocumentTraduction
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
     * @ORM\Column(name="libelle", type="string", length=100)
     */
    private $libelle;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Document")
     * @ORM\JoinColumn(name="idDocument", referencedColumnName="id")
     */
    private $document;
/**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Langue", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="idLangue", referencedColumnName="id")    
     */
    private $langue;
    
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
     *  Get document
     *
     * @return \BanquemondialeBundle\Entity\Document
     */
    public function getDocument()
    {
        return $this->document;
    }
    
    /**
     * Set document
     *
     * @param \BanquemondialeBundle\Entity\Document $document
     * @return DocumentTraduction
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }
    
    /**
     * Set libelle
     *
     * @param string $libelle
     * @return DocumentTraduction
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
    
    public function __toString()
	{
		return (String)$this->getLibelle();
	}

    /**
     * Set langue
     *
     * @param \BanquemondialeBundle\Entity\Langue $langue
     * @return DocumentTraduction
     */
    public function setLangue(\BanquemondialeBundle\Entity\Langue $langue = null)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return \BanquemondialeBundle\Entity\Langue 
     */
    public function getLangue()
    {
        return $this->langue;
    }
}
