<?php
require __DIR__ . '/vendor/autoload.php';
require("./webservicesCredentials.php");

use Automattic\WooCommerce\Client;

class WoocommerceClient
{

     private $PRECOR = "PR01";
     private $MAXCO = "EM01";

     private function getCredentials()
     {
          return new WebservicesCredentials();
     }

     private function isMaxco($id_soc)
     {
          if ($id_soc == "EM01") {
               return true;
          } else if ($id_soc == "MA01") {
               return true;
          } else {
               return false;
          }
     }

     private function isPrecor($id_soc)
     {
          if ($id_soc == "PR01") {
               return true;
          } else {
               return false;
          }
     }

     public function getWoocommerce($id_soc)
     {
          $credenciales = $this->getCredentials();
          if ($this->isMaxco($id_soc)) {
               /* maxco */
               return new Client(
                    $credenciales->MAXCO_URL,
                    $credenciales->WOO_MAXCO_CK,
                    $credenciales->WOO_MAXCO_CS,
                    [
                         'version' => 'wc/v3',
                    ]
               );
          } else if ($this->isPrecor($id_soc)) {
               /* precor */
               return new Client(
                    $credenciales->PRECOR_URL,
                    $credenciales->WOO_PRECOR_CK,
                    $credenciales->WOO_PRECOR_CS,
                    [
                         'version' => 'wc/v3',
                    ]
               );
          } else if ($id_soc == 999) {
               return new Client(
                    "http://maxco.punkuhr.test/",
                    "ck_0157c4f5fbc72b4a71161b929dea276a81006fd9",
                    "cs_b575ce513cbaf2478ca0d06c2d0dd64699ec642d",
                    [
                         'version' => 'wc/v3',
                    ]
               );
          } else if ($id_soc == 1000) {
               return new Client(
                    "http://precor.punkurhr.test/",
                    "ck_c005d91e27f8bc9b2b5df1328651092f23fd813c",
                    "cs_9af7943cc0d48db3f4cee10d9ba4dd6dee5395f2",
                    [
                         'version' => 'wc/v3',
                    ]
               );
          }
     }
}
