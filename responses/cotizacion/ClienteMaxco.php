<?php

class ClienteMaxco
{
    public $nomb;
    public $nrdoc;
    public $check_fact;
    public $drcfisc;
    public $correo;
    public $codubig;

    public function __construct($nomb, $nrdoc, $check_fact, $drcfisc, $correo, $codubig)
    {
        $this->nomb = $nomb;
        $this->nrdoc = $nrdoc;
        $this->check_fact = $check_fact;
        $this->drcfisc = $drcfisc;
        $this->correo = $correo;
        $this->codubig = $codubig;
    }
}
