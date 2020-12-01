<?php

class Cotizacion
{
     private $id_ctwb;
     private $cd_cli;
     private $direcdest;
     private $codpostal;
     private $lat;
     private $long;
     private $tpodesp;
     private $tpcotz;
     private $prctotal;
     private $materials;

     public function __construct($id_ctwb, $cd_cli, $direcdest, $codpostal, $lat, $long, $tpodesp, $tpcotz, $prctotal, $materials = [])
     {
          $this->id_ctwb = $id_ctwb;
          $this->cd_cli = $cd_cli;
          $this->direcdest = $direcdest;
          $this->codpostal = $codpostal;
          $this->lat = $lat;
          $this->long = $long;
          $this->tpodesp = $tpodesp;
          $this->tpcotz = $tpcotz;
          $this->prctotal = $prctotal;
          $this->materials = $materials;
     }
}
