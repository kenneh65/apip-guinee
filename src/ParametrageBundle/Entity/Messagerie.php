<?php

namespace ParametrageBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Messagerie
 *
 * @ORM\Table(name="messagerie")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\MessagerieRepository")
 */
class Messagerie
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
     * @ORM\Column(name="mailer_host", type="string", length=255,nullable=false)
	 * @Assert\NotBlank(message="messagerie.hote_non_nul")	 
     */
    private $mailerHost;

    /**
     * @var string
     *
     * @ORM\Column(name="mailer_user", type="string", length=255,nullable=true)
	 */
    private $mailerUser;

    /**
     * @var string
     *
     * @ORM\Column(name="mailer_password", type="string", length=255,nullable=true)
    */
    private $mailerPassword;
	
    /**
     * @var string
     *
     * @ORM\Column(name="mailer_port", type="string", length=255,nullable=true)
     */
    private $mailerPort;
	
    /**
     * @var string
     *
     * @ORM\Column(name="encryption", type="string", length=255,nullable=true)
     */
    private $encryption;
	
    /**
     * @var string
     *
     * @ORM\Column(name="expediteur_name", type="string", length=255,nullable=true)
     */
    private $expediteurName;
	
    /**
     * @var string
     *
     * @ORM\Column(name="expediteur_adresse", type="string", length=255,nullable=true)
	 * @Assert\Regex(pattern="/^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/",
     *     match=true,
     *     message="Votre adresse email n'est pas valide")	 
	 * @Assert\NotBlank(message="messagerie.mail_expiditeur_null")	 
     */
    private $expediteurEmail;
	
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
     * Set mailerHost
     *
     * @param string $mailerHost
     * @return Messagerie
     */
    public function setMailerHost($mailerHost)
    {
        $this->mailerHost = $mailerHost;

        return $this;
    }

    /**
     * Get mailerHost
     *
     * @return string 
     */
    public function getMailerHost()
    {
        return $this->mailerHost;
    }

    /**
     * Set mailerUser
     *
     * @param string $mailerUser
     * @return Messagerie
     */
    public function setMailerUser($mailerUser)
    {
        $this->mailerUser = $mailerUser;

        return $this;
    }

    /**
     * Get mailerUser
     *
     * @return string 
     */
    public function getMailerUser()
    {
        return $this->mailerUser;
    }

    /**
     * Set mailerPassword
     *
     * @param string $mailerPassword
     * @return Messagerie
     */
    public function setMailerPassword($mailerPassword)
    {
        $this->mailerPassword = $mailerPassword;

        return $this;
    }

    /**
     * Get mailerPassword
     *
     * @return string 
     */
    public function getMailerPassword()
    {
        return $this->mailerPassword;
    }

    /**
     * Set mailerPort
     *
     * @param string $mailerPort
     * @return Messagerie
     */
    public function setMailerPort($mailerPort)
    {
        $this->mailerPort = $mailerPort;

        return $this;
    }

    /**
     * Get mailerPort
     *
     * @return string 
     */
    public function getMailerPort()
    {
        return $this->mailerPort;
    }
	
	/**
     * Constructor
     */
    public function __construct()
    {	
		$this->mailerTransport = 'smtp';
		$this->encryption = 'ssl';
		$this->mailerPort = '465';
		$this->expediteurName = 'Gainde 2000';
		$this->expediteurEmail = 'adresse@aaa.aa';
	}

    /**
     * Set encryption
     *
     * @param string $encryption
     * @return Messagerie
     */
    public function setEncryption($encryption)
    {
        $this->encryption = $encryption;

        return $this;
    }

    /**
     * Get encryption
     *
     * @return string 
     */
    public function getEncryption()
    {
        return $this->encryption;
    }

    /**
     * Set expediteurName
     *
     * @param string $expediteurName
     * @return Messagerie
     */
    public function setExpediteurName($expediteurName)
    {
        $this->expediteurName = $expediteurName;

        return $this;
    }

    /**
     * Get expediteurName
     *
     * @return string 
     */
    public function getExpediteurName()
    {
        return $this->expediteurName;
    }

    /**
     * Set expediteurEmail
     *
     * @param string $expediteurEmail
     * @return Messagerie
     */
    public function setExpediteurEmail($expediteurEmail)
    {
        $this->expediteurEmail = $expediteurEmail;

        return $this;
    }

    /**
     * Get expediteurEmail
     *
     * @return string 
     */
    public function getExpediteurEmail()
    {
        return $this->expediteurEmail;
    }
}
