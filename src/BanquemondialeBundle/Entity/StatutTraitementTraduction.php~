<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StatutTraitementTraduction
 *
 * @ORM\Table(name="statuttraitementtraduction")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\StatutTraitementTraductionRepository")
 */
class StatutTraitementTraduction
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
     * @ORM\Column(name="libelle", type="string", length=80)
     */
    private $libelle;
	
	
	/**	
      * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\StatutTraitement", cascade={"persist","remove"})
      * @ORM\JoinColumn(name="idStatutTraitement", referencedColumnName="id")
      */
    private $statutTraitement;
	
	
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
     * @return StatutTraitementTraduction
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
     * Set statutTraitement
     *
     * @param integer $statutTraitement
     * @return StatutTraitementTraduction
     */
    public function setStatutTraitement($statutTraitement)
    {
        $this->statutTraitement = $statutTraitement;

        return $this;
    }

    /**
     * Get statutTraitement
     *
     * @return integer 
     */
    public function getStatutTraitement()
    {
        return $this->statutTraitement;
    }

    /**
     * Set langue
     *
     * @param integer $langue
     * @return StatutTraitementTraduction
     */
    public function setLangue($langue)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return integer 
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
