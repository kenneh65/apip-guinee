<?php

namespace UtilisateursBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProfileTraduction
 *
 * @ORM\Table(name="profile_traduction")
 * @ORM\Entity(repositoryClass="UtilisateursBundle\Repository\ProfileTraductionRepository")
 */
class ProfileTraduction
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;
	
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
	
     /**
      * @ORM\ManyToOne(targetEntity="Profile", inversedBy="profileTraduction", cascade={"persist"})
      * @ORM\JoinColumn(name="idProfile", referencedColumnName="id")
      */ 
    private $profile;
   
    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Langue", cascade={"persist"})
     * @ORM\JoinColumn(name="idLangue", referencedColumnName="id")    
     */
    private $langue;
	
	public function __construct()
    {
		$this->setEstDesactive(false);
    }
	
    /**
     * @var bool
     *
     * @ORM\Column(name="est_desactive", type="boolean")
     */
    private $estDesactive;

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
     * Set nom
     *
     * @param string $nom
     * @return ProfileTraduction
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ProfileTraduction
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set estDesactive
     *
     * @param boolean $estDesactive
     * @return ProfileTraduction
     */
    public function setEstDesactive($estDesactive)
    {
        $this->estDesactive = $estDesactive;

        return $this;
    }

    /**
     * Get estDesactive
     *
     * @return boolean 
     */
    public function getEstDesactive()
    {
        return $this->estDesactive;
    }

    /**
     * Set profile
     *
     * @param \UtilisateursBundle\Entity\Profile $profile
     * @return ProfileTraduction
     */
    public function setProfile(\UtilisateursBundle\Entity\Profile $profile = null)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return \UtilisateursBundle\Entity\Profile 
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set langue
     *
     * @param \BanquemondialeBundle\Entity\Langue $langue
     * @return ProfileTraduction
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
    public function __toString() {
        return $this->nom;
    }
}
