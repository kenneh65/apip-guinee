<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DefaultBundle\Entity;
/**
 * Class DossierReponseJson
 * @package DefaultBundle\Entity
 */
class DossierReponseJson {

    /**
     *  @return string
     */
    private $status;

    /**
     *  @return string
     */
    private $numeroDossier;

    /**
     * @return mixed
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * @param mixed $dateCreation
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
    }

    /**
     *  @return string
     */
    private $dateCreation;

    /**
     *  @return string
     */
    private $rccm;

    /**
     * @return mixed
     */
    public function getRccm()
    {
        return $this->rccm;
    }

    /**
     * @param mixed $rccm
     */
    public function setRccm($rccm)
    {
        $this->rccm = $rccm;
    }

    /**
     * @return mixed
     */
    public function getNif()
    {
        return $this->nif;
    }

    /**
     * @param mixed $nif
     */
    public function setNif($nif)
    {
        $this->nif = $nif;
    }

    /**
     *  @return string
     */
    private $nif;


    /**
     * @return string
     */
    public function getNumeroDossier()
    {
        return $this->numeroDossier;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param string $numeroDossier
     */
    public function setNumeroDossier($numeroDossier)
    {
        $this->numeroDossier = $numeroDossier;
    }

    /**
     * @return string
     */
    public function getDenominationCommercial()
    {
        return $this->denominationCommercial;
    }

    /**
     * @param string $denominationCommercial
     */
    public function setDenominationCommercial($denominationCommercial)
    {
        $this->denominationCommercial = $denominationCommercial;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param array $messages
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    private $denominationCommercial;


    /**
     * @var array
     *
     */
    private $messages;
}
