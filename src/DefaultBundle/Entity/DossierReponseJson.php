<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DefaultBundle\Entity;

/**
 * Description of ReponseJson
 *
 * @author fgueye
 */
class ReponseJson {
    /**
     * @var string
     *
     */
    private $numeroDossier;
	
	/**
     * @var string
     *
     */
    private $code;
	
	/**
     * @var string
     *     
     */
    private $description;
    
    function getNumeroDossier() {
        return $this->numeroDossier;
    }

    function getCode() {
        return $this->code;
    }

    function setNumeroDossier($numeroDossier) {
        $this->numeroDossier = $numeroDossier;
    }

    function setCode($code) {
        $this->code = $code;
    }
    function getDescription() {
        return $this->description;
    }

    function setDescription($description) {
        $this->description = $description;
    }



}
class ReponseJson2 {   
	
	/**
     * @var string
     *
     */
    private $code;
	
	/**
     * @var string
     *     
     */
    private $description;
    
    function getCode() {
        return $this->code;
    }

    function getDescription() {
        return $this->description;
    }

    function setCode($code) {
        $this->code = $code;
    }

    function setDescription($description) {
        $this->description = $description;
    }


}
