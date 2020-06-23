<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FAQ
 *
 * @ORM\Table(name="f_a_qs")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\FAQRepository")
 */
class FAQ
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
     * @ORM\Column(name="question", type="text")
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="reponse", type="text")
     */
    private $reponse;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime")
     */
    private $dateModification;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse_ip", type="string", length=11)
     */
    private $adresseIp;

     /**
     * @ORM\OneToMany(targetEntity="FAQTraduction",mappedBy="faq",cascade={"remove","persist"})
     */
    private  $traduction;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDateModification(new \DateTime());
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
     * Set question
     *
     * @param string $question
     * @return FAQ
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set reponse
     *
     * @param string $reponse
     * @return FAQ
     */
    public function setReponse($reponse)
    {
        $this->reponse = $reponse;

        return $this;
    }

    /**
     * Get reponse
     *
     * @return string 
     */
    public function getReponse()
    {
        return $this->reponse;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     * @return FAQ
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime 
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set adresseIp
     *
     * @param string $adresseIp
     * @return FAQ
     */
    public function setAdresseIp($adresseIp)
    {
        $this->adresseIp = $adresseIp;

        return $this;
    }

    /**
     * Get adresseIp
     *
     * @return string 
     */
    public function getAdresseIp()
    {
        return $this->adresseIp;
    }

    /**
     * Add traduction
     *
     * @param \ParametrageBundle\Entity\FAQTraduction $traduction
     * @return FAQ
     */
    public function addTraduction(\ParametrageBundle\Entity\FAQTraduction $traduction)
    {
        $this->traduction[] = $traduction;

        return $this;
    }

    /**
     * Remove traduction
     *
     * @param \ParametrageBundle\Entity\FAQTraduction $traduction
     */
    public function removeTraduction(\ParametrageBundle\Entity\FAQTraduction $traduction)
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
}
