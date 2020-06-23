<?php

namespace DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statistique
 *
 * @ORM\Table(name="statistique")
 * @ORM\Entity(repositoryClass="DefaultBundle\Repository\StatistiqueRepository")
 */
class Statistique
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
     * @ORM\Column(name="adresse_ip", type="string", length=14)
     */
    private $adresseIp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_visite", type="datetime")
     */
    private $dateVisite;


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
     * Set adresseIp
     *
     * @param string $adresseIp
     * @return Statistique
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
     * Set dateVisite
     *
     * @param \DateTime $dateVisite
     * @return Statistique
     */
    public function setDateVisite($dateVisite)
    {
        $this->dateVisite = $dateVisite;

        return $this;
    }

    /**
     * Get dateVisite
     *
     * @return \DateTime 
     */
    public function getDateVisite()
    {
        return $this->dateVisite;
    }
    public function __construct($adresse_ip, $dateVisite) {
        $this->adresseIp=$adresse_ip;
        $this->dateVisite = $dateVisite;
    }
}
