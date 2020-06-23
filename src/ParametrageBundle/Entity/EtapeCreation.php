<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EtapeCreation
 *
 * @ORM\Table(name="etapecreation")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\EtapeCreationRepository")
 */
class EtapeCreation {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    
    /**
     * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\Fonctionnalite")
     * @ORM\JoinColumn(name="idEtape", referencedColumnName="id")
     */
    private $etape;

    /**
     * @var string
     *
     * @ORM\Column(name="urlEtape", type="string", length=80,nullable=true)
     */
    private $urlEtape;

    /**
     * @var int
     *
     * @ORM\Column(name="ordre", type="integer")
     */
    private $ordre;
    
    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\FormeJuridique")
     * @ORM\JoinColumn(name="idFormeJuridique", referencedColumnName="id")
     */
    private $formeJuridique;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }
     
    /**
     * Set urlEtape
     *
     * @param string $urlEtape
     * @return EtapeCreation
     */
    public function setUrlEtape($urlEtape) {
        $this->urlEtape = $urlEtape;

        return $this;
    }

    /**
     * Get urlEtape
     *
     * @return string 
     */
    public function getUrlEtape() {
        return $this->urlEtape;
    }


    /**
     * Set ordre
     *
     * @param integer $ordre
     * @return EtapeCreation
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer 
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set etape
     *
     * @param \ParametrageBundle\Entity\Fonctionnalite $etape
     * @return EtapeCreation
     */
    public function setEtape(\ParametrageBundle\Entity\Fonctionnalite $etape = null)
    {
        $this->etape = $etape;

        return $this;
    }

    /**
     * Get etape
     *
     * @return \ParametrageBundle\Entity\Fonctionnalite 
     */
    public function getEtape()
    {
        return $this->etape;
    }

    /**
     * Set formeJuridique
     *
     * @param \BanquemondialeBundle\Entity\FormeJuridique $formeJuridique
     * @return EtapeCreation
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
}
