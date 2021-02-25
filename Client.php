<?php
class Client
{
     public $id_soc;
     public $cd_cli;
     public $nrdoc;
     public $nomb;
     public $telf;
     public $telfmov;
     public $email;
     public $drcfisc;
     public $city;
     public $distr;
     public $codubig;
     public $obs;
     public $cod;
     // nuevos campos
     public $cond_pago;
     public $descrip_cond_pago;
     function __construct($data = [])
     {
          $this->id_soc = $data["id_soc"];
          $this->cd_cli = $data["cd_cli"];
          $this->nrdoc = $data["nrdoc"];
          $this->nomb = $data["nomb"];
          $this->telf = $data["telf"];
          $this->telfmov = $data["telfmov"];
          $this->email = $data["email"];
          $this->drcfisc = $data["drcfisc"];
          $this->city = $data["city"];
          $this->distr = $data["distr"];
          $this->codubig = $data["codubig"];
          $this->obs = $data["obs"];
          $this->cod = $data["cod"];
          // nuevos campos
          $this->cond_pago = $data["cond_pago"];
          $this->descrip_cond_pago = $data["descrip_cond_pago"];
     }
}
