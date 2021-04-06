<?php 
class ResponseObject
{
     public $RPTA;
     public $DETA;
     public $STUS;
     public $DATA;
     function __construct($RPTA = 0, $DETA = 'Sin detalle que mostrar', $STUS = 200, $DATA = "Sin Data que mostrar")
     {
          $this->RPTA = $RPTA;
          $this->DETA = $DETA;
          $this->STUS = $STUS;
          $this->DATA = $DATA;
     }
}
