<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\validator\Constraints as Assert;


/**
* Media
*
* @ORM\Table("media")
* @ORM\Entity(repositoryClass="EcommerceBundle\Repository\MediaRepository")
* @ORM\HasLifecycleCallbacks
*/
class Media
{
	/**
	* @var integer
	*
	* @ORM\Column(name="id", type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	private $id;

	/**
	* @var \DateTime
	*
	* @ORM\COlumn(name="updated_at",type="datetime", nullable=true)
	*/
	private $updateAt;

	/**
	* @ORM\PostLoad()
	*/
	public function postLoad()
	{
		$this->updateAt = new \DateTime();
	}



	/**
	* @ORM\Column(type="string",length=255, nullable=true)
	*/
	private $path;
	
	/**
	* @ORM\Column(type="string",length=255, nullable=true)
	*/
	private $extension;
	

	public $file;
	
	public $cheminUpload="C:\\dossier\\";

	public function getUploadRootDir()
	{	
		return  $this->getCheminUpload().$this->getUploadDir();
	}

	public function getAbsolutePath()
	{
		
		return null === $this->path ? null : $this->getUploadRootDir().'\\'.$this->path;
	}

	public function getAssetPath()
	{
		
		return $this->getUploadRootDir().'\\'.$this->path;
	}

	/**
	* @ORM\OneToOne(targetEntity="BanquemondialeBundle\Entity\Documentation", mappedBy="fichier")
	* @ORM\JoinColumn(nullable=true)
	*/
	private $documentation;
	
	/**
	* @ORM\OneToOne(targetEntity="ParametrageBundle\Entity\ImageSlider", mappedBy="fichier")
	* @ORM\JoinColumn(nullable=true)
	*/
	private $imageSlider;
	
	protected function getUploadDir()
	{
		// on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
		// le document/image dans la vue.
		
		if($this->documentation!=null)
		return 'Guides';
		else
		return '';
	}
	/**
	* @ORM\Prepersist()
	* @ORM\Preupdate()
	*/
	public function preUpload()
	{
		$this->tempFile = $this->getAbsolutePath();
		$this->oldFile = $this->getPath();
		$this->updateAt = new \DateTime();

		if (null !== $this->file)
		{
			$this->extension=$this->file->guessExtension();
			if($this->documentation!=null)
				$this->path = $this->documentation->getTitre().'.'.$this->file->guessExtension();
			else
				$this->path = $this->imageSlider->getTitre().'.'.$this->file->guessExtension();
		}
	}

	
	/**
	* @ORM\PostPersist()
	* @ORM\PostUpdate()
	*/
	public function upload()
	{
		if (null !== $this->file) {		
			$this->file->move($this->getUploadRootDir(),$this->path);
			unset($this->file);

			
			if ($this->oldFile != null) 
			{
				if(file_exists($this->tempFile))
				{
					unlink($this->tempFile);
				}
			}
		}
	}

	/**
	* @ORM\PreRemove()
	*/
	public function preRemoveUpload()
	{
		$this->tempFile = $this->getAbsolutePath();
	}

	/**
	* @ORM\PostRemove()
	*/
	public function removeUpload()
	{
		if (file_exists($this->tempFile)) unlink($this->tempFile);
	}

	

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


	/**
	* Set path
	*
	* @param string $path
	* @return Media
	*/
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

	/**
	* Get path
	*
	* @return string 
	*/
	public function getPath()
	{
		return $this->path;
	}

	/**
	* Set extension
	*
	* @param string $extension
	* @return Media
	*/
	public function setExtension($extension)
	{
		$this->extension = $extension;

		return $this;
	}

	/**
	* Get extension
	*
	* @return string 
	*/
	public function getExtension()
	{
		return $this->extension;
	}
	
	
	/**
	* Set cheminUpload
	*
	* @param string $cheminUpload
	* @return Media
	*/
	public function setCheminUpload($cheminUpload)
	{
		$this->cheminUpload = $cheminUpload;

		return $this;
	}

	/**
	* Get cheminUpload
	*
	* @return string 
	*/
	public function getCheminUpload()
	{
		return $this->cheminUpload;
	}
	
	
	/**
	* Set documentation
	*
	* @param \BanquemondialeBundle\Entity\Documentation $documentation
	* @return Documentation
	*/
	public function setDocumentation(\BanquemondialeBundle\Entity\Documentation $documentation=null)
	{
		$this->documentation = $documentation;

		return $this;
	}

	/**
	* Get documentation
	*
	* @return \BanquemondialeBundle\Entity\Documentation 
	*/
	public function getDocumentation()
	{
		return $this->documentation;
	}
	
	
	/**
     * Set imageSlider
     *
     * @param \ParametrageBundle\Entity\ImageSlider $imageSlider
     * @return Media
     */
    public function setImageSlider(\ParametrageBundle\Entity\ImageSlider $imageSlider = null)
    {
        $this->imageSlider = $imageSlider;

        return $this;
    }

    /**
     * Get imageSlider
     *
     * @return \ParametrageBundle\Entity\ImageSlider 
     */
    public function getImageSlider()
    {
        return $this->imageSlider;
    }
	
    public function getWebPath() 
	{
	    return $this->getUploadDir() . '/' . $this->path;
    }


}
