<?php
require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

class WoocommerceClient
{

     public function getWoocommerce($id_soc)
     {
          if (intval($id_soc) == 1) {
               /* maxco */
               return new Client(
                    "https://maxco.punkuhr.com/",
                    "ck_0157c4f5fbc72b4a71161b929dea276a81006fd9",
                    "cs_b575ce513cbaf2478ca0d06c2d0dd64699ec642d",
                    [
                         'version' => 'wc/v3',
                    ]
               );
          } else {
               /* precor */
               return new Client(
                    "https://precor.punkuhr.com/",
                    "ck_c005d91e27f8bc9b2b5df1328651092f23fd813c",
                    "cs_9af7943cc0d48db3f4cee10d9ba4dd6dee5395f2",
                    [
                         'version' => 'wc/v3',
                    ]
               );
          }
     }
}
