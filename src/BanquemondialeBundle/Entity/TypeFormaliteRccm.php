<?php

namespace BanquemondialeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeFormaliteRccm
 *
 * @ORM\Table(name="typeformaliterccm")
 * @ORM\Entity(repositoryClass="BanquemondialeBundle\Repository\TypeFormaliteRccmRepository")
 */
class TypeFormaliteRccm
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
     * @ORM\Column(name="Libelle", type="string", length=255)
     */
    private $libelle;


    /**
     * @var string
     *
     * @ORM\Column(name="typeFormulaire", type="string", length=5,nullable=true)
     */
    private $typeFormulaire;
    
    /**
     * @var string
     *
     * @ORM\Column(name="lettreFormulaire", type="string", length=2,nullable=false)
     */
    private $lettreFormulaire;
    
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
     * Set libelle
     *
     * @param string $libelle
     * @return TypeFormaliteRccm
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set typeFormulaire
     *
     * @param string $typeFormulaire
     * @return TypeFormaliteRccm
     */
    public function setTypeFormulaire($typeFormulaire)
    {
        $this->typeFormulaire = $typeFormulaire;

        return $this;
    }

    /**
     * Get typeFormulaire
     *
     * @return string 
     */
    public function getTypeFormulaire()
    {
        return $this->typeFormulaire;
    }

    /**
     * Set lettreFormulaire
     *
     * @param string $lettreFormulaire
     * @return TypeFormaliteRccm
     */
    public function setLettreFormulaire($lettreFormulaire)
    {
        $this->lettreFormulaire = $lettreFormulaire;

        return $this;
    }

    /**
     * Get lettreFormulaire
     *
     * @return string 
     */
    public function getLettreFormulaire()
    {
        return $this->lettreFormulaire;
    }
}
