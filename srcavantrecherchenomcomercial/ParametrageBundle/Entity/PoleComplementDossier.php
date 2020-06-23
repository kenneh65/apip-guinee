<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PoleComplementDossier
 *
 * @ORM\Table(name="polecomplementdossier")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\PoleComplementDossierRepository")
 */
class PoleComplementDossier
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
     * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\ComplementPole")
     * @ORM\JoinColumn(name="idChampPole", referencedColumnName="id")
     */
    private $complementpole;
    
    /**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\DossierDemande")
      * @ORM\JoinColumn(name="idDossierDemande", referencedColumnName="id")
      */
    private $dossierDemande;
    
    /**
     * @var string
     *
     * @ORM\Column(name="Valeur", type="string", length=255, nullable=true)
     */
    private $valeur;


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
     * Set valeur
     *
     * @param string $valeur
     * @return PoleCompelentDossier
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get valeur
     *
     * @return string 
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * Set complementpole
     *
     * @param \ParametrageBundle\Entity\ComplementPole $complementpole
     * @return PoleComplementDossier
     */
    public function setComplementpole(\ParametrageBundle\Entity\ComplementPole $complementpole = null)
    {
        $this->complementpole = $complementpole;

        return $this;
    }

    /**
     * Get complementpole
     *
     * @return \ParametrageBundle\Entity\ComplementPole 
     */
    public function getComplementpole()
    {
        return $this->complementpole;
    }

    /**
     * Set dossierDemande
     *
     * @param \BanquemondialeBundle\Entity\DossierDemande $dossierDemande
     * @return PoleComplementDossier
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
}
