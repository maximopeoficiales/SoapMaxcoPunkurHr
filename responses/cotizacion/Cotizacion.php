<?php

class Cotizacion
{
     private $id_ctwb;
     private $cd_cli;
     private $prctotal;
     private $materials;

     public function __construct($id_ctwb, $cd_cli, $prctotal, $materials = [])
     {
          $this->id_ctwb = $id_ctwb;
          $this->cd_cli = $cd_cli;
          $this->prctotal = $prctotal;
          $this->materials = $materials;
     }
}
