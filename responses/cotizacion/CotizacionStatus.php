<?php
class CotizacionStatus
{
     private $stpag;
     private $dscrp;
     private $tpcob;
     private Niubiz $obsniubiz;

     public function __construct($stpag, $dscrp, $tpcob,Niubiz $obsniubiz)
     {
          $this->stpag = $stpag;
          $this->dscrp = $dscrp;
          $this->tpcob = $tpcob;
          $this->obsniubiz = $obsniubiz;
     }
}
