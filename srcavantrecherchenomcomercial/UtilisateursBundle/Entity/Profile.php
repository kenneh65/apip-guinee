<?php
namespace UtilisateursBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Profile
 *
 * @ORM\Table(name="profile")
 * @ORM\Entity(repositoryClass="UtilisateursBundle\Repository\ProfileRepository")
 */
class Profile
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
      * @ORM\OneToMany(targetEntity="ProfileTraduction",mappedBy="profile", cascade={"persist","remove"})
      */ 
    private $profileTraduction;
	
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @ORM\ManyToOne(targetEntity="ParametrageBundle\Entity\Pole")
     * @ORM\joinColumn(nullable=true)
     */
    private $pole;

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
     * @return Profile
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
     * @return Profile
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
     * @return Profile
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
     * Set pole
     *
     * @param \ParametrageBundle\Entity\Pole $pole
     * @return Profile
     */
    public function setPole(\ParametrageBundle\Entity\Pole $pole = null)
    {
        $this->pole = $pole;

        return $this;
    }

    /**
     * Get pole
     *
     * @return \ParametrageBundle\Entity\Pole 
     */
    public function getPole()
    {
        return $this->pole;
    }

    /**
     * Add profileTraduction
     *
     * @param \UtilisateursBundle\Entity\ProfileTraduction $profileTraduction
     * @return Profile
     */
    public function addProfileTraduction(\UtilisateursBundle\Entity\ProfileTraduction $profileTraduction)
    {
        $this->profileTraduction[] = $profileTraduction;

        return $this;
    }

    /**
     * Remove profileTraduction
     *
     * @param \UtilisateursBundle\Entity\ProfileTraduction $profileTraduction
     */
    public function removeProfileTraduction(\UtilisateursBundle\Entity\ProfileTraduction $profileTraduction)
    {
        $this->profileTraduction->removeElement($profileTraduction);
    }

    /**
     * Get profileTraduction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProfileTraduction()
    {
        return $this->profileTraduction;
    }
}
