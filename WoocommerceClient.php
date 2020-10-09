<?php
define('WP_USE_THEMES', false);
require('../wp-blog-header.php');
require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

class WoocommerceClient
{
     public function getDataConfig()
     {
          $args = array(
               'post_type' => 'max_functions_config',
               'posts_per_page' => 1,
               'orderby' => 'ID',
               'order' => 'ASC'
          );

          $datos = new WP_Query($args);
          $data = array();
          while ($datos->have_posts()) : $datos->the_post();
               $data["api_key_google_maps"] = get_field("api_key_google_maps");
               $data["consumer_key"] = get_field("consumer_key");
               $data["consumer_secret"] = get_field("consumer_secret");
               $data["latitud_tienda"] = get_field("latitud_tienda");
               $data["longitud_tienda"] = get_field("longitud_tienda");
          endwhile;
          return $data;
     }
     public function getWoocommerce()
     {
          $credenciales = $this->getDataConfig();
          $woocommerce = new Client(
               get_site_url(),
               $credenciales["consumer_key"],
               $credenciales["consumer_secret"],
               [
                    'version' => 'wc/v3',
               ]
          );
          return $woocommerce;
     }
}
