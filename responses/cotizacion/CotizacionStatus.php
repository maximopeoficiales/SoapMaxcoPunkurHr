<?php
class CotizacionStatus
{
     private $stpag;
     private $dscrp;
     private $tpcob;
     private Niubiz $obs_niubiz;

     public function __construct($stpag, $dscrp, $tpcob,Niubiz $obs_niubiz)
     {
          $this->stpag = $stpag;
          $this->dscrp = $dscrp;
          $this->tpcob = $tpcob;
          $this->obs_niubiz = $obs_niubiz;
     }
}
