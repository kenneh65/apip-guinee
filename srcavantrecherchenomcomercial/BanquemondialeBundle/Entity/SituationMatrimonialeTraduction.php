<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SituationMatrimonialeTraduction
 *
 * @ORM\Table(name="situationmatrimonialetraduction")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\SituationMatrimonialeTraductionRepository")
 */
class SituationMatrimonialeTraduction
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
     * @ORM\Column(name="libelle", type="string", length=30)
     */
    private $libelle;

	
	/**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\SituationMatrimoniale", cascade={"persist","remove"})
      * @ORM\JoinColumn(name="idSituationMatrimoniale", referencedColumnName="id")
      */
    private $situationMatrimoniale;
	
	
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
     * Set libelle
     *
     * @param string $libelle
     * @return SituationMatrimonialeTraduction
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

    /**
     * Set situationMatrimoniale
     *
     * @param \BanquemondialeBundle\Entity\SituationMatrimoniale $situationMatrimoniale
     * @return SituationMatrimonialeTraduction
     */
    public function setSituationMatrimoniale(\BanquemondialeBundle\Entity\SituationMatrimoniale $situationMatrimoniale = null)
    {
        $this->situationMatrimoniale = $situationMatrimoniale;

        return $this;
    }

    /**
     * Get situationMatrimoniale
     *
     * @return \BanquemondialeBundle\Entity\SituationMatrimoniale 
     */
    public function getSituationMatrimoniale()
    {
        return $this->situationMatrimoniale;
    }

    /**
     * Set langue
     *
     * @param \BanquemondialeBundle\Entity\Langue $langue
     * @return SituationMatrimonialeTraduction
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
	
	public function __toString()
	{
		return (String)$this->getLibelle();
	}
}
