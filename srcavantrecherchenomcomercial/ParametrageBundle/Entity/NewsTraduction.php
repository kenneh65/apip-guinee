<?php

namespace ParametrageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NewsTraduction
 *
 * @ORM\Table(name="news_traduction")
 * @ORM\Entity(repositoryClass="ParametrageBundle\Repository\NewsTraductionRepository")
 */
class NewsTraduction {

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
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;

    /**
     * @ORM\ManyToOne(targetEntity="BanquemondialeBundle\Entity\Langue")
     */
    private $langue;

    /**
     * @ORM\ManyToOne(targetEntity="News",inversedBy="traduction")
     */
    private $news;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return NewsTraduction
     */
    public function setTitre($titre) {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre() {
        return $this->titre;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     * @return NewsTraduction
     */
    public function setContenu($contenu) {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu() {
        return $this->contenu;
    }


    /**
     * Set langue
     *
     * @param \BanquemondialeBundle\Entity\Langue $langue
     * @return NewsTraduction
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
     * Set news
     *
     * @param \ParametrageBundle\Entity\News $news
     * @return NewsTraduction
     */
    public function setNews(\ParametrageBundle\Entity\News $news = null)
    {
        $this->news = $news;

        return $this;
    }

    /**
     * Get news
     *
     * @return \ParametrageBundle\Entity\News 
     */
    public function getNews()
    {
        return $this->news;
    }
      public function __construct()
    {
        $this->titre='';
        $this->contenu='';
    }
}
