<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FAQTraduction
 *
 * @ORM\Table(name="f_a_q_traduction")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\FAQTraductionRepository")
 */
class FAQTraduction
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
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Langue")
     */
    private $langue;
 /**
     * @ORM\ManyToOne(targetEntity="FAQ",inversedBy="traduction")
     */
    private  $faq;
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
     * @return FAQTraduction
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
     * @return FAQTraduction
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
     * Set langue
     *
     * @param \BanquemondialeBundle\Entity\Langue $langue
     * @return FAQTraduction
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

    /**
     * Set faq
     *
     * @param \ParametrageBundle\Entity\FAQ $faq
     * @return FAQTraduction
     */
    public function setFaq(\ParametrageBundle\Entity\FAQ $faq = null)
    {
        $this->faq = $faq;

        return $this;
    }

    /**
     * Get faq
     *
     * @return \ParametrageBundle\Entity\FAQ 
     */
    public function getFaq()
    {
        return $this->faq;
    }
     public function __construct()
    {
        $this->question='';
        $this->reponse='';
    }
}
