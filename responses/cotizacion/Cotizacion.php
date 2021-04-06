<?php

class Cotizacion
{
     private $id_ctwb;
     private $cd_cli;
     private $direcdest;
     private $codpostal;
     private Niubiz $obs_niubiz;
     private $payment_method;
     private $payment_method_title;
     private $lat;
     private $long;
     private $tpodesp;
     private $tpcotz;
     private $cod_status;
     private $status_desc;
     private $prctotal;
     private $materials;

     public function __construct($id_ctwb, $cd_cli, $cod_dest, Niubiz $obs_niubiz, $direcdest, $codpostal, $payment_method, $payment_method_title, $lat, $long, $tpodesp, $tpcotz, $cod_status, $status_desc, $prctotal, $materials = [])
     {
          $this->id_ctwb = $id_ctwb;
          $this->cd_cli = $cd_cli;
          $this->cod_dest = $cod_dest;
          $this->obs_niubiz = $obs_niubiz;
          $this->direcdest = $direcdest;
          $this->codpostal = $codpostal;
          $this->payment_method = $payment_method;
          $this->payment_method_title = $payment_method_title;
          $this->lat = $lat;
          $this->long = $long;
          $this->tpodesp = $tpodesp;
          $this->tpcotz = $tpcotz;
          $this->cod_status = $cod_status;
          $this->status_desc = $status_desc;
          $this->prctotal = $prctotal;
          $this->materials = $materials;
     }
}
