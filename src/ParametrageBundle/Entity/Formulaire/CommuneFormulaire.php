<?php
namespace ParametrageBundle\Entity\Formulaire;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CommuneFormulaire
{
    /** 
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 6,
     *      maxMessage = "Le nom ne doit pas depasser 6 characteres"
     * )
     *
     */
    private $code;
    /** 
	 * @var string
     * @Assert\NotBlank()
     * @Assert\NotNull()
	 * @Assert\Length(
     *      max = 80,
     *      maxMessage = "Le nom ne doit pas depasser 80 characteres"
     * )
     *
     */
    private $libelle;
    
    function getCode() {
        return $this->code;
    }

    function getLibelle() {
        return $this->libelle;
    }

    function setCode($code) {
        $this->code = $code;
    }

    function setLibelle($libelle) {
        $this->libelle = $libelle;
    }


}