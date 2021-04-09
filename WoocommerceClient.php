<?php
require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

class WoocommerceClient
{
     private $PRECOR = "PR01";
     private $MAXCO = "EM01";
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
          if ($this->isMaxco($id_soc)) {
               /* maxco */
               return new Client(
                    "https://maxco.punkuhr.com/",
                    "ck_0157c4f5fbc72b4a71161b929dea276a81006fd9",
                    "cs_b575ce513cbaf2478ca0d06c2d0dd64699ec642d",
                    [
                         'version' => 'wc/v3',
                    ]
               );
          } else if ($this->isPrecor($id_soc)) {
               /* precor */
               return new Client(
                    "https://tiendaqa.precor.pe/",
                    "ck_952cea875bbbb8bf80821580690da8679481e1d8",
                    "cs_be07378fc861abe7781d8c4a355e1ada70a88c13",
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
