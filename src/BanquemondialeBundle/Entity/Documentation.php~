<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Documentation
 *
 * @ORM\Table(name="documentation")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\DocumentationRepository")
 */
class Documentation
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
     * @ORM\OneToOne(targetEntity="BanquemondialeBundle\Entity\Media", inversedBy="documentation",cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true,onDelete="cascade")
       */
    private $fichier;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=100)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;
    /**
     * @ORM\OneToMany(targetEntity="DocumentationTraduction", mappedBy="documentation",cascade={"remove","persist"})
     */
    private $traduction;
	
	/**
	* @var \DateTime
	*
	* @ORM\COlumn(name="updated_at",type="datetime", nullable=true)
	*/
	private $updateAt;


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
     * Set titre
     *
     * @param string $titre
     * @return Documentation
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Documentation
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
     * Set fichier
     *
     * @param \BanquemondialeBundle\Entity\Media $fichier
     * @return Documentation
     */
    public function setFichier(\BanquemondialeBundle\Entity\Media $fichier=null)
    {
        $this->fichier = $fichier;

        return $this;
    }

    /**
     * Get fichier
     *
     * @return \BanquemondialeBundle\Entity\Media 
     */
    public function getFichier()
    {
        return $this->fichier;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->traduction = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fichier=new \BanquemondialeBundle\Entity\Media();
    }

    /**
     * Add traduction
     *
     * @param \BanquemondialeBundle\Entity\DocumentationTraduction $traduction
     * @return Documentation
     */
    public function addTraduction(\BanquemondialeBundle\Entity\DocumentationTraduction $traduction)
    {
        $this->traduction[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \BanquemondialeBundle\Entity\DocumentationTraduction $traduction
     */
    public function removeTraduction(\BanquemondialeBundle\Entity\DocumentationTraduction $traduction)
    {
        $this->traduction->removeElement($traduction);
    }

    /**
     * Get traduction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTraduction()
    {
        return $this->traduction;
    }
	
	/**
	* Set updateAt
	*
	* @param \DateTime $updateAt
	* @return Media
	*/
	public function setUpdateAt($updateAt)
	{
		$this->updateAt = $updateAt;

		return $this;
	}

	/**
	* Get updateAt
	*
	* @return \DateTime 
	*/
	public function getUpdateAt()
	{
		return $this->updateAt;
	}
}
