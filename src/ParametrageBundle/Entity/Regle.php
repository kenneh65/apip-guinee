<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Regle
 *
 * @ORM\Table(name="Regle")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\RegleRepository")
 */
class Regle
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
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\FormeJuridique", inversedBy="Regle")
      * @ORM\JoinColumn(name="idformeJuridique", referencedColumnName="id")
      */ 
    private $formeJuridique;
   
    /**
     * @var boolean
     * @ORM\Column(name="commissairerequis",type="boolean", options={"default":false})
     */
    private $commissaireRequis;

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
     * Set formeJuridique
     *
     * @param \BanquemondialeBundle\Entity\FormeJuridique $formeJuridique
     * @return Regle
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
     * Set residence
     *
     * @param boolean $commissaireRequis
     * @return regle
     */
    public function setCommissaireRequis($commissaireRequis)
    {
        $this->commissaireRequis = $commissaireRequis;

        return $this;
    }

    /**
     * Get commissaireRequis
     *
     * @return boolean 
     */
    public function getCommissaireRequis()
    {
        return $this->commissaireRequis;
    }
	
	
    public function __toString() {
        return $this->libelle;
    }
}
