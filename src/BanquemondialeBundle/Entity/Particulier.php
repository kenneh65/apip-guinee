<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Particulier
 *
 * @ORM\Table(name="particulier")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\ParticulierRepository")
 */
class Particulier
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
 * @ORM\OneToOne(targetEntity="UtilisateursBundle\Entity\Utilisateurs",mappedBy="particulier",cascade={"persist"})
 */
    private $utilisateur;

    /**
 * @ORM\OneToOne(targetEntity="UtilisateursBundle\Entity\Profile")
 */
    private $profil;
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
     * Set utilisateur
     *
     * @param \UtilisateursBundle\Entity\Utilisateurs $utilisateur
     * @return Particulier
     */
    public function setUtilisateur(\UtilisateursBundle\Entity\Utilisateurs $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \UtilisateursBundle\Entity\Utilisateurs 
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set profil
     *
     * @param \UtilisateursBundle\Entity\Profile $profil
     * @return Particulier
     */
    public function setProfil(\UtilisateursBundle\Entity\Profile $profil = null)
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * Get profil
     *
     * @return \UtilisateursBundle\Entity\Profile 
     */
    public function getProfil()
    {
        return $this->profil;
    }
}
