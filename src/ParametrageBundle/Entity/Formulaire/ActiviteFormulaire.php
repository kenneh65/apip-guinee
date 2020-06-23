<?php
namespace ParametrageBundle\Entity\Formulaire;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ActiviteFormulaire
{
    /** 
	 * @var string
	 * @Assert\Length(
     *      max = 4,
     *      maxMessage = "Le code ne doit pas depasser 4 characteres"
     * )
     *
     */
    private $code;
    /** 
	 * @var string
	 * @Assert\Length(
     *      max = 250,
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