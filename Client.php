<?php
class Client
{
     public $id_soc;
     public $cd_cli;
     public $nrdoc;
     public $nomb;
     public $telf;
     public $email;
     public $drcfisc;
     public $city;
     public $distr;
     public $codubig;
     public $drcdest;
     public $city2;
     public $distr2;
     public $codubig2;
     public $obs;
     public $cod;
     function __construct($data = [])
     {
          $this->id_soc = $data["id_soc"];
          $this->cd_cli = $data["cd_cli"];
          $this->nrdoc = $data["nrdoc"];
          $this->nomb = $data["nomb"];
          $this->telf = $data["telf"];
          $this->email = $data["email"];
          $this->drcfisc = $data["drcdest"];
          $this->city = $data["city"];
          $this->city2 = $data["city"];
          $this->distr = $data["distr"];
          $this->distr2 = $data["distr"];
          $this->codubig = $data["codubig"];
          $this->codubig2 = $data["codubig"];
          $this->drcdest = $data["drcdest"];
          $this->obs = $data["obs"];
          $this->cod = $data["cod"];
     }
}
