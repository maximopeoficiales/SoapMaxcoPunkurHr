<?php
class CotizacionStatus
{
     private $stpag;
     private $dscrp;
     private $tpcob;

     public function __construct($stpag, $dscrp, $tpcob)
     {
          $this->stpag = $stpag;
          $this->dscrp = $dscrp;
          $this->tpcob = $tpcob;
     }
}
