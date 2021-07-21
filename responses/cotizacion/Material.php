<?php

class Material
{

     private $pos;
     private $id_mat;
     private $nomb;
     private $cant;
     private $und;
     private $prec;
     private $prectot;
     private $dsct;
     public function __construct($pos, $id_mat, $nomb, $cant, $und, $prec, $dsct, $prectot)
     {
          $this->pos = $pos;
          $this->id_mat = $id_mat;
          $this->nomb = $nomb;
          $this->cant = $cant;
          $this->und = $und;
          $this->prec = $prec;
          $this->dsct = $dsct;
          $this->prectot = $prectot;
     }
}
