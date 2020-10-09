<?php
require "./WoocommerceClient.php";
define('WP_USE_THEMES', false);
require('../wp-blog-header.php');
class MethodsWoo
{
     private function getWoocommerce()
     {
          $woo = new WoocommerceClient();
          return $woo->getWoocommerce();
     }

     public function CreateMaterialWoo($material)
     {

          $woo = $this->getWoocommerce();
          $weight = number_format($material["peso"], 2, ".", "");
          $sku = $material["id_mat"];
          $dataSend = [
               'name' => $material["nomb"],
               'sku' => $sku,
               'weight' => $weight,
               "meta_data" => [],
          ];
          if ($material["und"] !== "kg") {
               $dataSend["meta_data"] = [
                    [
                         "key" => "und",
                         "value" => $material['und'],
                    ],
                    [
                         "key" => "und_value",
                         "value" =>  $weight,
                    ]
               ];
          }
          $id_soc = $material["id_soc"];
          $newfields = ["id_soc", "cent", "alm", "jprod"];
          foreach ($this->mfAddNewFieldsMetadata($material, $newfields) as  $value) {
               array_push($dataSend["meta_data"], $value);
          }
          if ($id_soc == "MAX") {
               /* creacion */
               if ($material["cod"] == 0) {
                    try {
                         $response = $woo->post('products', $dataSend); //devuelve un objeto
                         if ($response->id !== null) {
                              return [
                                   "value" => 1,
                                   "data" => $response,
                                   // "data" => ["id" => $response->id, "permalink" => $response->permalink],
                                   "message" => "Registro de Material Exitoso",
                              ];
                         }
                    } catch (\Throwable $th) {
                         return [
                              "value" => 0,
                              "message" => "EL SKU: $sku ya existe",
                         ];
                    }
               } else {
                    /* actualizacion */
                    try {
                         $id_cliente = $this->mfGetIdMaterialWithSku($sku);
                         $this->mfUpdateMetadataMaterial($id_cliente, $dataSend["meta_data"]);
                         $response = $this->mfUpdateMaterialWithSku($sku, $dataSend);
                         return [
                              "value" => 2,
                              "message" => "Material con sku: $sku actualizado",
                              "data" => $response
                         ];
                    } catch (\Throwable $th) {
                         return [
                              "value" => 0,
                              "message" => "El material con el sku: $sku no existe",
                         ];
                    }
               }
          } else {
               return [
                    "value" => 0,
                    "message" => "El id_soc: $id_soc no coincide con nuestra sociedad",
               ];
          }
     }
     private function mfGetIdMaterialWithSku($sku)
     {
          $woo = $this->getWoocommerce();
          $findMaterial = $woo->get("products", ["sku" => $sku]);
          return $findMaterial[0]->id;
     }
     private function mfUpdateMaterialWithSku($sku, $dataUpdated)
     {
          $woo = $woo = $this->getWoocommerce();
          $findMaterial = $woo->get("products", ["sku" => $sku]);
          $response = $woo->put("products/" . $findMaterial[0]->id, $dataUpdated);
          return $response;
     }
     private function mfUpdateMetadataMaterial($id_cliente, $data)
     {
          for ($i = 0; $i < count($data); $i++) {
               $dato = $data[$i];
               global $wpdb;
               $table = $wpdb->base_prefix . 'postmeta';
               $sql = "UPDATE $table SET  meta_value = %s where post_id=$id_cliente AND meta_key=%s";
               $result = $wpdb->query($wpdb->prepare($sql, $dato["value"], $dato["key"]));
               $wpdb->flush();
               if (!$result) new Error("Error en la actualizacion de  datos");
          }
     }
     private function mfAddNewFieldsMetadata($dataCurrent, $fields)
     {
          $metadata = [];
          foreach ($fields as $value) {
               array_push($metadata, ["key" => $value, "value" => $dataCurrent[$value]]);
          }
          return $metadata;
     }
}
