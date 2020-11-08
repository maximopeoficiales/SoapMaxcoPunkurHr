<?php
require "./WoocommerceClient.php";
define('WP_USE_THEMES', false);
require('../wp-blog-header.php');
class MethodsWoo
{
     /* constantes */
     private $PRECOR = "PR01";
     private $MAXCO = "EM01";
     private function getWPDB($id_soc)
     {

          if (($id_soc) === $this->MAXCO) {
               /* maxco */
               return new wpdb('i5142852_wp4', 'F.L7tJxfhTbrfbpP7Oe41', 'i5142852_wp4', 'localhost');
          } else if (($id_soc) === $this->PRECOR) {
               /* precor */
               return new wpdb('i5142852_wp7', 'O.WfNQrZjiDKYtz507j13', 'i5142852_wp7', 'localhost');
          } else if (($id_soc) == 999) {
               /* mi localhost */
               return new wpdb('root', '', 'maxcopunkuhr', 'localhost:3307');
          }
     }
     private function getWoocommerce($id_soc)
     {
          $woo = new WoocommerceClient();
          return $woo->getWoocommerce($id_soc);
     }
     /* Materiales */
     public function UpdateMaterialStockWoo($material)
     {
          $id_soc = $material["id_soc"];
          if (($id_soc) == $this->MAXCO || ($id_soc) == $this->PRECOR) {
               $sku = $material["id_mat"];
               $dataUpdated = [
                    "stock_quantity" => $material["stck"],
               ];
               if ($material["stck"] == 0) {
                    $dataUpdated["manage_stock"] = false;
               } else {
                    $dataUpdated["manage_stock"] = true;
               }
               $metadata = [];
               $newfields = ["id_soc", "jprod", "undpaq", "und"];
               foreach ($this->mfAddNewFieldsMetadata($material, $newfields) as  $value) {
                    array_push($metadata, $value);
               }
               try {
                    $id_cliente = $this->mfGetIdMaterialWithSku($sku, $id_soc);
                    $this->mfUpdateMetadataMaterial($id_cliente, $metadata, $id_soc);
                    $response = $this->mfUpdateMaterialWithSku($sku, $dataUpdated, $id_soc);
                    return [
                         "value" => 2,
                         "message" => "Material con sku: $sku actualizado",
                         "data" => "El stock restante es: " .  $response->stock_quantity,
                    ];
               } catch (\Throwable $th) {
                    return [
                         "value" => 0,
                         "message" => "El material con el sku: $sku no existe",
                    ];
               }
          } else {
               return [
                    "value" => 0,
                    "message" => "El id_soc: $id_soc no coincide con nuestra sociedad",
               ];
          }
     }
     public function CreateMaterialWoo($material)
     {

          $weight = number_format($material["peso"], 3, ".", "");
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
          $woo = $this->getWoocommerce($id_soc);
          $newfields = ["id_soc", "paq", "undpaq", "paqxun", "unxpaq", "jprod"];
          foreach ($this->mfAddNewFieldsMetadata($material, $newfields) as  $value) {
               array_push($dataSend["meta_data"], $value);
          }

          if (($id_soc) == $this->MAXCO || ($id_soc) == $this->PRECOR) {
               /* creacion */
               if ($material["cod"] == 0) {
                    try {
                         $response = $woo->post('products', $dataSend); //devuelve un objeto
                         if ($response->id !== null) {
                              return [
                                   "value" => 1,
                                   "data" => "id_mat: " .  $response->sku, " permalink: " . $response->permalink,
                                   "message" => "Registro de Material Exitoso",
                              ];
                         }
                    } catch (\Throwable $th) {
                         return [
                              "value" => 0,
                              "message" => "EL SKU: $sku ya existe",
                         ];
                    }
               } else if ($material["cod"] == 1) {
                    /* actualizacion */
                    try {
                         $id_cliente = $this->mfGetIdMaterialWithSku($sku, $id_soc);
                         $this->mfUpdateMetadataMaterial($id_cliente, $dataSend["meta_data"], $id_soc);
                         $response = $this->mfUpdateMaterialWithSku($sku, $dataSend, $id_soc);
                         return [
                              "value" => 2,
                              "message" => "Material con sku: $sku actualizado",
                         ];
                    } catch (\Throwable $th) {
                         return [
                              "value" => 0,
                              "message" => "El material con el sku: $sku no existe",
                         ];
                    }
               } else {
                    $cod = $material["cod"];
                    return [
                         "value" => 0,
                         "message" => "El cod : $cod enviado no es valido",
                    ];
               }
          } else {
               return [
                    "value" => 0,
                    "message" => "El id_soc: $id_soc no coincide con nuestra sociedad",
               ];
          }
     }

     private function mfGetIdMaterialWithSku($sku, $id_soc)
     {
          $woo = $this->getWoocommerce($id_soc);
          $findMaterial = $woo->get("products", ["sku" => $sku]);
          return $findMaterial[0]->id;
     }
     private function mfUpdateMaterialWithSku($sku, $dataUpdated, $id_soc)
     {
          $woo = $woo = $this->getWoocommerce($id_soc);
          $findMaterial = $woo->get("products", ["sku" => $sku]);
          $response = $woo->put("products/" . $findMaterial[0]->id, $dataUpdated);
          return $response;
     }
     private function mfUpdateMetadataMaterial($id, $data, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          for ($i = 0; $i < count($data); $i++) {
               $dato = $data[$i];
               $sql = "UPDATE wp_postmeta SET  meta_value = %s where post_id=$id AND meta_key=%s";
               $result = $wpdb->query($wpdb->prepare($sql, $dato["value"], $dato["key"]));
               $wpdb->flush();
               if (!$result) new Error("Error en la actualizacion de  datos");
          }
     }
     /* fin de materiales */
     /*  Clientes */
     public function UpdateClientWoo($cliente)
     {
          $id_soc = $cliente["id_soc"];
          if (($id_soc) == $this->MAXCO || ($id_soc) == $this->PRECOR) {
                         
               
               return [
                    "value" => 1,
                    "message" => "Todo Correcto",
               ];
          } else {
               return [
                    "value" => 0,
                    "message" => "El id_soc: $id_soc no coincide con nuestra sociedad",
               ];
          }
     }
     /*  Fin Clientes */
     /* Creditos */
     public function UpdateCreditoWoo($credito)
     {
          $id_soc = $credito["id_soc"];
          $cd_cli = $credito["cd_cli"];
          $id_client = $credito["id_cli"];
          $mntdisp = $credito["mntdisp"];
          if (($id_soc) == $this->MAXCO || ($id_soc) == $this->PRECOR) {
               try {
                    $field_data = ["id_cli" => $id_client, "mntcred" => $credito["mntcred"], "mntutil" => $credito["mntutil"], "fvenc" => $credito["fvenc"]];
                    // $field_data = ["Ejecutivo_ventas" => $cd_cli, "Telefono_asesor" => $cd_cli];
                    $this->mfUpdateFieldsCredito($id_soc, $id_client, $field_data, $mntdisp) ? true : new Error();
                    return [
                         "value" => 2,
                         "message" => "Credito con el id_cli: $id_client actualizado",
                         "data" => "Monto Disponible: " . $mntdisp
                    ];
               } catch (\Throwable $th) {
                    return [
                         "value" => 0,
                         "message" => "El Credito  con el id_cli: $id_client no existe",
                    ];
               }
          } else {
               return [
                    "value" => 0,
                    "message" => "El id_soc: $id_soc no coincide con nuestra sociedad",
               ];
          }
     }

     private function mfUpdateFieldsCredito($id_soc, $id_client, $fields_data, $mntdisp)
     {
          try {
               $data = $this->mfGetDataPFCredito($id_soc, $fields_data);
               // $wpdb = $this->getWPDB(999);
               $wpdb = $this->getWPDB($id_soc);
               //     UPDATE wp_prflxtrflds_user_field_data SET user_value = "1111111" WHERE user_id = 8 AND field_id=2;
               /* update profile fields */
               for ($i = 0; $i < count($data); $i++) {
                    $dato = $data[$i];
                    $id = $dato["id"];
                    $update = $dato["update"];
                    $sql = "UPDATE wp_prflxtrflds_user_field_data SET user_value = %s WHERE user_id = $id_client AND field_id=$id";
                    $result = $wpdb->query($wpdb->prepare($sql, $update));
                    $wpdb->flush();
                    if (!$result) new Error("Error en la actualizacion de  datos");
               }
               /* update wallet balancec */
               //UPDATE wp_fswcwallet SET balance = "80" WHERE user_id = 3
               $sqlwallet = "UPDATE wp_fswcwallet SET balance = %s WHERE user_id = $id_client";
               $resultw = $wpdb->query($wpdb->prepare($sqlwallet, $mntdisp));
               $wpdb->flush();
               if (!$resultw) new Error("Error en la actualizacion de  datos");
               return true;
          } catch (\Throwable $th) {
               return false;
          }
     }
     private function mfGetDataPFCredito($id_soc, $fields_data)
     {
          $fields_filtered = [];
          // $wpdb = $this->getWPDB(999);
          $wpdb = $this->getWPDB($id_soc);
          $results = $wpdb->get_results("select field_id,field_name from wp_prflxtrflds_fields_id");
          foreach ($results as $value) {
               foreach ($fields_data as $key => $valueUpdated) {
                    if ($value->field_name == strval($key)) {
                         array_push($fields_filtered, ["id" => $value->field_id, "field_name" => $value->field_name, "update" => $valueUpdated]);
                    }
               }
          }
          return $fields_filtered;
     }
     /* fin de creditos */
     private function mfAddNewFieldsMetadata($dataCurrent, $fields)
     {
          $metadata = [];
          foreach ($fields as $value) {
               array_push($metadata, ["key" => $value, "value" => $dataCurrent[$value]]);
          }
          return $metadata;
     }
}
