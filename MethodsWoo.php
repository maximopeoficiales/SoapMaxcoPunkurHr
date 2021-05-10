<?php


require "./WoocommerceClient.php";
define('WP_USE_THEMES', false);
require('../wp-blog-header.php');
date_default_timezone_set('America/Lima');
require "./Client.php";
//respuesta de cotizacion
require "./responses/cotizacion/Cotizacion.php";
require "./responses/cotizacion/Material.php";
require "./responses/cotizacion/CotizacionStatus.php";
require "./responses/cotizacion/Niubiz.php";
require "./translate/Translate.php";
require "./utilities/Utilities.php";

// require "./webservicesCredentials.php";
class MethodsWoo
{
     /* constantes */
     private $PRECOR_URL;
     private $MAXCO_URL;
     public function __construct()
     {
          $this->PRECOR_URL = $this->getCredentials()->PRECOR_URL;
          $this->MAXCO_URL = $this->getCredentials()->MAXCO_URL;
     }

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

     private function getWPDB($id_soc)
     {
          $credenciales = $this->getCredentials();
          if ($this->isMaxco($id_soc)) {
               /* maxco */
               return new wpdb($credenciales->DB_MAXCO_USER, $credenciales->DB_MAXCO_PASS, $credenciales->DB_MAXCO_DBNAME, $credenciales->HOST_DB);
          } else if ($this->isPrecor($id_soc)) {
               /* precor */
               return new wpdb($credenciales->DB_PRECOR_USER, $credenciales->DB_PRECOR_PASS, $credenciales->DB_PRECOR_DBNAME, $credenciales->HOST_DB);
          } else if (999) {
               /* mi localhost */
               return new wpdb('root', '', 'maxcopunkuhr', 'localhost:3307');
          } else if ($id_soc == 1000) {
               return new wpdb('root', '', 'precorpunkuhr', 'localhost:3307');
          }
     }
     private function getWoocommerce($id_soc)
     {
          $woo = new WoocommerceClient();
          return $woo->getWoocommerce($id_soc);
     }

     // tipo de cambio
     public function updateTypeRate($data_currency)
     {
          $id_soc = $data_currency["id_soc"];
          $tipo_cambio = $data_currency["tipo_cambio"];
          $fecha_cambio = $data_currency["fecha_cambio"];

          if ($this->isPrecor($id_soc) || $this->isMaxco($id_soc)) {
               if ($this->saveTipoCambio($tipo_cambio, $fecha_cambio, $id_soc)) {
                    // se inserto correctamente el registro
                    if ($this->executeJobUpdateTypeRate($id_soc)) {
                         return [
                              "value" => 2,
                              "message" => "Se guardo y se actualizo el tipo de cambio",
                         ];
                    } else {
                         return [
                              "value" => 0,
                              "message" => "Error en la actualizacion de tipo de cambio, pero tipo de cambio fue guardado",
                         ];
                    };
               } else {
                    return [
                         "value" => 0,
                         "message" => "Error en el registro tipo de cambio",
                    ];
               }
          } else {
               return [
                    "value" => 0,
                    "message" => "El id_soc $id_soc no es valido",
               ];
          }
     }
     private function saveTipoCambio($tipo_cambio, $fecha_cambio, $id_soc): bool
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "INSERT INTO wp_tipo_cambio (tipo_cambio,created_at)  VALUES (%s,%s) ";
          $result = $wpdb->query($wpdb->prepare($sql, $tipo_cambio, $fecha_cambio));
          $wpdb->flush();
          return $result;
     }
     private function getTiposCambioMaxcoPrecor($id_soc): string
     {
          $fecha_actual = date("Y-m-d");
          $sql = "SELECT * FROM wp_tipo_cambio WHERE DATE_FORMAT(created_at,'%Y-%m-%d') = '$fecha_actual' ORDER BY created_at DESC LIMIT 1";
          $wdpbPrecor = $this->getWPDB($id_soc);
          $resultPrecor = $wdpbPrecor->get_results($sql)[0];
          return $resultPrecor->tipo_cambio;
     }
     private function updateTypeRateWebservice($urlDomain, $type_rate): bool
     {
          $curl = curl_init();

          curl_setopt_array($curl, array(
               CURLOPT_URL => $urlDomain . 'wp-json/webservices_precor/v1/update_currency_rate',
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 0,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'POST',
               CURLOPT_POSTFIELDS => '{"user":"PRECOR","pass":"PRECOR2","rate":"' . $type_rate . '"}',
               CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
               ),
          ));
          $response = curl_exec($curl);
          curl_close($curl);
          // echo $response;
          $response = json_decode($response, true);
          return $response["data"]["status"] == 200 ? true : false;
          // return true;
     }
     private function executeJobUpdateTypeRate($id_soc): bool
     {
          $tipoCambio = $this->getTiposCambioMaxcoPrecor($id_soc);
          if ($tipoCambio != null) {
               if ($this->isPrecor($id_soc)) {
                    return $this->updateTypeRateWebservice($this->PRECOR_URL, $tipoCambio);
               } else if ($this->isMaxco($id_soc)) {
                    return $this->updateTypeRateWebservice($this->MAXCO_URL, $tipoCambio);
               }
          }
     }
     // fin de tipo de cambio

     /* Materiales */
     public function UpdateMaterialStockWoo($material)
     {
          $id_soc = $material["id_soc"];
          if ($this->isMaxco($id_soc) ||  $this->isPrecor($id_soc)) {
               $sku = intval($material["id_mat"]);
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
               $metadata["undpaq"] = null ? "" : $metadata["undpaq"];
               try {
                    $user_ide = $this->mfGetIdMaterialWithSku($sku, $id_soc);
                    $this->mfUpdateMetadataMaterial($user_ide, $metadata, $id_soc);
                    $response = (object) $this->mfUpdateMaterialWithSku($sku, $dataUpdated, $id_soc);
                    return [
                         "value" => 2,
                         "message" => "Material con sku: $sku actualizado",
                         "data" => "El stock restante es: " .  $response->stock_quantity,
                    ];
               } catch (\Throwable $th) {
                    return [
                         "value" => 0,
                         "message" => "El material con el sku: $sku no existe, Error: $th",
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
          $material["peso"] = $weight;
          $sku = strval(intval($material["id_mat"]));
          $dataSend = [
               'name' => $material["nomb"],
               'sku' => $sku,
               'weight' => $weight,
               "meta_data" => [],
          ];
          if ($material["und"] !== "kg") {
               $dataSend["meta_data"] = [
                    [
                         "key" => "und_value",
                         "value" =>  $weight,
                    ]
               ];
          }
          $id_soc = $material["id_soc"];
          $jprod = $material["jprod"];
          // campos para vender por paquete
          $material["group_of_quantity"] = $material["unxpaq"] ?? "";
          $material["minimum_allowed_quantity"] = $material["unxpaq"] ?? "";
          // fin de campos
          $woo = $this->getWoocommerce($id_soc);
          $newfields = ["id_soc", "paq", "undpaq", "paqxun", "unxpaq", "jprod", "und", "group_of_quantity", "minimum_allowed_quantity"];
          // los agrego al metadata
          foreach ($this->mfAddNewFieldsMetadata($material, $newfields) as  $value) {
               array_push($dataSend["meta_data"], $value);
          }
          array_push($dataSend["meta_data"], ["key" => "peso", "value" => $weight]); //funcion con acf

          if ($this->isMaxco($id_soc) ||  $this->isPrecor($id_soc)) {
               /* creacion */
               if ($material["cod"] == 0) {
                    try {
                         $response = (object) $woo->post('products', $dataSend); //devuelve un objeto
                         // este procedure solo se ejecuta cambia la categoria segun el jprod
                         if ($this->isPrecor($id_soc)) {
                              try {
                                   $wpdb = $this->getWPDB($id_soc);
                                   $sql = "CALL producto_categoria({$response->id},%s)";
                                   $wpdb->query($wpdb->prepare($sql, $jprod));
                                   $wpdb->flush();
                              } catch (\Throwable $th) {
                                   return [
                                        "value" => 0,
                                        "message" => "Error: $th",
                                   ];
                              }
                         }
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
                              "message" => "EL SKU: $sku ya existe error: $th",
                         ];
                    }
               } else if ($material["cod"] == 1) {
                    /* actualizacion */
                    try {
                         $id_material = $this->mfGetIdMaterialWithSku($sku, $id_soc);
                         $this->mfUpdateMetadataMaterial($id_material, $dataSend["meta_data"], $id_soc);
                         $response = $this->mfUpdateMaterialWithSku($sku, $dataSend, $id_soc);
                         // este procedure solo se ejecuta cambia la categoria segun el jprod
                         if ($this->isPrecor($id_soc)) {
                              try {
                                   $wpdb = $this->getWPDB($id_soc);
                                   $sql = "CALL producto_categoria($id_material,%s)";
                                   $wpdb->query($wpdb->prepare($sql, $jprod));
                                   $wpdb->flush();
                              } catch (\Throwable $th) {
                                   return [
                                        "value" => 0,
                                        "message" => "Error: $th",
                                   ];
                              }
                         }
                         return [
                              "value" => 2,
                              "message" => "Material con sku: $sku actualizado",
                         ];
                    } catch (\Throwable $th) {
                         return [
                              "value" => 0,
                              "message" => "El material con el sku: $sku no existe, Error: $th",
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
     public function updateMaterialPrice($material)
     {
          $id_soc = $material["id_soc"];
          $id_mat = strval(intval($material["id_mat"]));
          $price = $material["prec"];
          $categ = strtoupper($material["categ"]);
          $dataSend = [
               "price" => $price,
               "regular_price" => $price,
               "sale_price" => $price,
          ];
          if ($this->isMaxco($id_soc) ||  $this->isPrecor($id_soc)) {
               /* creacion y actualizacion */
               // $id_soc = 999;
               $metadata = [];
               $id_material = $this->mfGetIdMaterialWithSku($id_mat, $id_soc);
               try {
                    $this->getWoocommerce($id_soc)->put("products/$id_material", $dataSend);
                    $newfields = ["canal", "categ"];
                    foreach ($this->mfAddNewFieldsMetadata($material, $newfields) as  $value) {
                         array_push($metadata, $value);
                    }
                    // $fieldsCreate = ["canal" => $material["canal"], "categ" => $material["categ"]];

                    if ($this->ExistsFieldMaterialMetadata("canal", $id_material, $id_soc)) {
                         $this->mfUpdateMetadataMaterial($id_material, $metadata, $id_soc);
                    } else {
                         if ($material["categ"] == "") {
                              $material["categ"] = "VACIO";
                         }
                         $this->createFieldMaterialMetadata("canal", $material["canal"], $id_material, $id_soc);
                         $this->createFieldMaterialMetadata("categ", $material["categ"], $id_material, $id_soc);
                    }

                    // este procedure solo se ejecuta si
                    if ($this->isPrecor($id_soc)) {
                         try {
                              $wpdb = $this->getWPDB($id_soc);
                              $sql = "CALL procedure_update_rol_precio($id_material,%s,$price)";
                              $wpdb->query($wpdb->prepare($sql, $categ));
                              $wpdb->flush();
                         } catch (\Throwable $th) {
                              return [
                                   "value" => 0,
                                   "message" => "Error: $th",
                              ];
                         }
                    }
                    return [
                         "value" => 2,
                         "message" => "Precio de Material $id_mat Actualizado",
                    ];
               } catch (\Throwable $th) {
                    return [
                         "value" => 0,
                         "message" => "No existe el producto, Error:  $th",
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
     // actualiza en bruto todo los campos enviados por parametro
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
     // verifica si existe el campo en el metadata
     private function ExistsFieldMaterialMetadata($field, $id_material, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "SELECT * FROM wp_postmeta WHERE post_id=$id_material AND meta_key=%s LIMIT 1";
          $result = $wpdb->get_results($wpdb->prepare($sql, $field));
          // $wpdb->flush();
          return (count($result) !== 0) ? true : false;
     }
     // crea campo en el metadata
     private function createFieldMaterialMetadata($key, $value, $id_material, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "INSERT INTO wp_postmeta (post_id,meta_key,meta_value)  VALUES ($id_material,%s,%s) ";
          $wpdb->query($wpdb->prepare($sql, $key, $value));
          $wpdb->flush();
          return true;
     }
     // actualiza el campo en el metadata
     private function updateFieldMaterialMetadata($key, $value, $post_id, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "UPDATE wp_postmeta SET meta_value = %s WHERE post_id = $post_id AND meta_key = %s";
          $wpdb->query($wpdb->prepare($sql, $value, $key));
          $wpdb->flush();
          return true;
     }
     /* fin de materiales */


     private function createOrUpdateWhenExistsMetaValue($key, $value, $post_id, $id_soc)
     {
          if ($this->ExistsFieldMaterialMetadata($key, $post_id, $id_soc)) {
               // actualiza el meta valor
               $this->updateFieldMaterialMetadata($key, $value, $post_id, $id_soc);
          } else {
               // crea el meta valor
               $this->createFieldMaterialMetadata($key, $value, $post_id, $id_soc);
          }
          return true;
     }

     /*  Clientes */
     public function GetClientsWoo($params)
     {


          $id_soc = $params["id_soc"];
          $fecini = $params["fecini"];
          $fecfin = $params["fecfin"];

          //get clients solo esta habilitado para maxco
          if ($this->isMaxco($id_soc)) {
               // $id_soc = 999;
               $response = $this->getClientsByDate($id_soc, $fecini, $fecfin);
               if ($response == null) {
                    return [
                         "value" => 0,
                         "message" => "No hay Clientes entre : $fecini - $fecfin",
                    ];
               } else {
                    return [
                         "value" => 1,
                         "message" => "Clientes entre : $fecini - $fecfin",
                         "data" => $response
                    ];
               }
          } else {
               return [
                    "value" => 0,
                    "message" => "El id_soc: $id_soc no coincide con nuestra sociedad",
               ];
          }
     }
     public function UpdateClientWoo($cliente)
     {
          $cliente["cod_postal"] = $cliente["cod_postal"] == null ? "07001" : $cliente["cod_postal"];
          $cliente["dest_cod_postal"] = $cliente["dest_cod_postal"] == null ? "07001" : $cliente["dest_cod_postal"];
          $params = array(
               "id_dest" => $cliente["id_dest"],
               "first_name" => $cliente["nomb"],
               "last_name" => "",
               "company" => $cliente["nrdoc"],
               "country" => "PE",
               "address_1" => $cliente["drcdest"],
               "address_2" => "",
               "postcode" => $cliente["cod_postal"],
               "phone" => $cliente["telf"],
               "email" => $cliente["email"]
          );
          $id_soc = $cliente["id_soc"];
          $cod = $cliente["cod"];
          $id_dest = $cliente["id_dest"];
          if ($this->isMaxco($id_soc) ||  $this->isPrecor($id_soc)) {
               // $cliente["id_soc"] = 999;
               /* creacion */
               if ($cod == 0) {
                    return $this->createCliente($cliente, true, true);
               } else if ($cod == 1) {
                    return $this->UpdateCliente($cliente, false);
               } else if ($cod == 2) {
                    //solo crea destinatarios
                    if ($id_dest != "" || $cliente["drcdest"] != "") {
                         $user_id = $this->getUserIDForId_cli($cliente["id_cli"], $id_soc);
                         // linea necesario para que pueda crear destinatarios solo con el email
                         if ($user_id == null) {
                              $user_id = $this->getUserIDByEmail($cliente["email"], $id_soc);
                         }
                         $cd_cli = $this->getCdCliWithUserIdSap($user_id, $id_soc);
                         // para solo la creacion de destinatarios usa el campo dest_cod_postal
                         $params["postcode"] = $cliente["dest_cod_postal"];
                         if ($this->createAddressSoap($user_id, $params)) {
                              return [
                                   "value" => 1,
                                   "message" => "El id_dest : $id_dest ha sido creado ",
                                   "data" => "cd_cli: $cd_cli",
                              ];
                         } else {
                              return [
                                   "value" => 0,
                                   "message" => "El id_dest : $id_dest ya existe ",
                              ];
                         }
                    } else {
                         return [
                              "value" => 0,
                              "message" => "El id_dest o drcdest vacio por favor rellenelo",
                         ];
                    }
               } else if ($cod == 3) {
                    // crea cliente y direccion
                    if ($id_dest != "" || $cliente["drcdest"] != "" || $cliente["dest_cod_postal"] != "") {
                         return $this->createCliente($cliente, true);
                    } else {
                         return [
                              "value" => 0,
                              "message" => "El id_dest o drcdest o dest_cod_postal  vacio por favor rellenelo",
                         ];
                    }
                    /*crea cliente y crea direccion  */
                    /* actualiza destinatarios */
                    // $this->createRecipientAddress($cliente, 1);
                    // $user_id = $this->getUserIDForId_cli($cliente["id_cli"], $id_soc);
                    // if ($this->createAddressSoap($user_id, $params, true)) {
                    //      return [
                    //           "value" => 2,
                    //           "message" => "El id_dest : $id_dest ha sido actualizado ",
                    //      ];
                    // }
               } else if ($cod == 4) {
                    /*actualizar cliente y actualizar direccion  */
                    if ($id_dest != "" || $cliente["drcdest"] != "" || $cliente["dest_cod_postal"] != "") {
                         return $this->UpdateCliente($cliente, true);
                    } else {
                         return [
                              "value" => 0,
                              "message" => "El id_dest o drcdest o dest_cod_postal vacio por favor rellenelo",
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
     private function createCliente($cliente, $activeDest = false, $copiarAddress = false)
     {
          $id_soc = $cliente["id_soc"];
          $id_cli = $cliente["id_cli"];
          $cond_pago = $cliente["cond_pago"];
          $descrip_cond_pago = $cliente["descrip_cond_pago"];
          $categ = $cliente["categ"];
          $dataSend = [
               'email' => $cliente["email"],
               'first_name' => $cliente["nomb"],
               'username' => $cliente["email"],
               'password' => "123456789",
               'billing' => [
                    "address_1" => $cliente["drcfisc"] == null ? "" : $cliente["drcfisc"],
                    'email' => $cliente["email"],
                    'phone' => $cliente["telfmov"] == null ? "" : $cliente["telfmov"],
                    'postcode' => $cliente["cod_postal"],
               ],
          ];
          $email = $cliente["email"];
          if (!$this->verifyEmail($email, $id_soc)) {
               return [
                    "value" => 0,
                    "message" => "El email: $email ya existe",
               ];
          }
          if (!$this->verifyId_cli($id_cli, $id_soc)) {
               return [
                    "value" => 0,
                    "message" => "El id_cli: $id_cli ya existe en nuestra base de datos",
               ];
          }

          try {
               // este metodo crea el cliente si no devuelve null sigue con los demas metodos
               $response = (object)$this->getWoocommerce($id_soc)->post('customers', $dataSend); //devuelve un objeto
               if ($response->id !== null) {
                    try {
                         $cd_cli = $this->getCd_CliSap($response->id, ["date_created" => $response->date_created], $id_soc);
                    } catch (\Throwable $th) {
                         return [
                              "value" => 0,
                              "message" => "Error al generar el cd_cli",
                         ];
                    }

                    // creacion de cond_pago y descripcion_cond_pago
                    try {
                         $this->createMetaValueByKey("cond_pago", $cond_pago, $response->id, $id_soc);
                         $this->createMetaValueByKey("descrip_cond_pago", $descrip_cond_pago, $response->id, $id_soc);
                    } catch (\Throwable $th) {
                         return [
                              "value" => 0,
                              "message" => "Error al crear los campos en cond_pago y status_desc Error: $th",
                         ];
                    }
                    try {
                         $this->createPFXFieldsClient($response->id,  $cliente, $id_soc);
                    } catch (\Throwable $th) {
                         return [
                              "value" => 0,
                              "message" => "Error al crear los campos en PFX",
                         ];
                    }

                    try {
                         if ($activeDest  && $id_soc == $this->isPrecor($id_soc)) {
                              $id_dest = $copiarAddress ? 1 : $cliente["id_dest"];
                              $params = array(
                                   "id_dest" => $copiarAddress ? 1 : $id_dest,
                                   "first_name" => $cliente["nomb"],
                                   "last_name" => "",
                                   "company" => $cliente["nrdoc"],
                                   "country" => "PE",
                                   "address_1" => $copiarAddress ? $cliente["drcfisc"] : $cliente["drcdest"],
                                   "address_2" => "",
                                   "postcode" =>  $copiarAddress ? $cliente["cod_postal"] : $cliente["dest_cod_postal"],
                                   "phone" => $cliente["telfmov"],
                                   "email" => $cliente["email"]
                              );
                              // $user_id = $this->getUserIDForId_cli($cliente["id_cli"], $id_soc);
                              if ($this->createAddressSoap($response->id, $params)) {
                                   return [
                                        "value" => 1,
                                        "data" => "cd_cli: $cd_cli|id_dest: $id_dest",
                                        "message" => "Registro de Cliente y direccion Exitosa",
                                   ];
                              } else {
                                   return [
                                        "value" => 0,
                                        "data" => "cd_cli: " .  $cd_cli,
                                        "message" => "Se creo cliente pero hubo error en creacion de direccion id_dest: $id_dest ya registrado",
                                   ];
                              }
                         }
                    } catch (\Throwable $th) {
                         return [
                              "value" => 0,
                              "message" => "Error en la creacion de destinatarios",
                         ];
                    }
                    // llamado a call user_role($user_id)
                    if ($this->isPrecor($id_soc)) {
                         try {
                              $wpdb = $this->getWPDB($id_soc);
                              $sql = "CALL user_role({$response->id},%s)";
                              $wpdb->query($wpdb->prepare($sql, $categ));
                              $wpdb->flush();
                         } catch (\Throwable $th) {
                              return [
                                   "value" => 0,
                                   "message" => "Error: $th",
                              ];
                         }
                    }
                    return [
                         "value" => 1,
                         "data" => "cd_cli: " .  $cd_cli,
                         "message" => "Registro de Cliente Exitoso",
                    ];
               }
          } catch (\Throwable $th) {
               return [
                    "value" => 0,
                    "message" => "Error en la creacion de cliente,
                    los datos ingresados son id_soc: $id_soc" . " email: " . $cliente["email"] . " nomb: " . $cliente["nomb"] . " drcfisc: " . $cliente["drcfisc"] . " telfmov: " . $cliente["telfmov"] . ",error: $th",
               ];
          }
     }
     private function UpdateCliente($cliente, $activeDest = false)
     {
          /* actualizacion */
          $id_soc = $cliente["id_soc"];
          $id_cli = $cliente["id_cli"];
          $cond_pago = $cliente["cond_pago"];
          $descrip_cond_pago = $cliente["descrip_cond_pago"];
          $categ = $cliente["categ"];
          $dataSend = [
               'email' => $cliente["email"],
               'first_name' => $cliente["nomb"],
               'billing' => [
                    'email' => $cliente["email"],
                    'phone' => $cliente["telfmov"],
                    'address_1' => $cliente["drcfisc"],
                    // 'company' => $cliente["nrdoc"],
                    'postcode' => $cliente["cod_postal"],
                    'country' => "PE",
               ],
          ];
          try {
               //cuando tenga un id_cli si es null busca por email
               $user_id = $this->getUserIDForId_cli($id_cli, $id_soc);
               if ($user_id == null) {
                    $user_id = $this->getUserIDByEmail($cliente["email"], $id_soc);
               }
               if ($user_id != null) {
                    $this->getWoocommerce($id_soc)->put("customers/$user_id", $dataSend); //devuelve un objeto

                    // actualiza o crea todos los campos pfx
                    $this->updatePFXFieldsClient($user_id,  $cliente, $id_soc);
                    $cd_cli = $this->getCdCliWithUserIdSap($user_id, $id_soc);
                    // actualizo los nuevos campos
                    try {
                         $this->updateMetaValueByKey("cond_pago", $cond_pago, $user_id, $id_soc);
                         $this->updateMetaValueByKey("descrip_cond_pago", $descrip_cond_pago, $user_id, $id_soc);
                    } catch (\Throwable $th) {
                         return [
                              "value" => 0,
                              "message" => "No se actualizaron los campos cond_pago y status_desc,Error: $th",
                         ];
                    }
                    // llamado a call user_role($user_id,$categ)
                    if ($this->isPrecor($id_soc)) {
                         try {
                              $wpdb = $this->getWPDB($id_soc);
                              $sql = "CALL user_role($user_id,%s)";
                              $wpdb->query($wpdb->prepare($sql, $categ));
                              $wpdb->flush();
                         } catch (\Throwable $th) {
                              return [
                                   "value" => 0,
                                   "message" => "Error: $th",
                              ];
                         }
                    }
                    if ($activeDest && $id_soc == $this->isPrecor($id_soc)) {
                         $id_dest = $cliente["id_dest"];
                         $params = array(
                              "id_dest" => $cliente["id_dest"],
                              "first_name" => $cliente["nomb"],
                              "last_name" => "",
                              "company" => $cliente["nrdoc"],
                              "country" => "PE",
                              "address_1" => $cliente["drcdest"],
                              "address_2" => "",
                              "postcode" => $cliente["dest_cod_postal"],
                              "phone" => $cliente["telfmov"],
                              "email" => $cliente["email"]
                         );
                         // $user_id = $this->getUserIDForId_cli($cliente["id_cli"], $id_soc);
                         if ($this->createAddressSoap($user_id, $params, true)) {
                              return [
                                   "value" => 2,
                                   "message" => "Cliente con id_cli: $id_cli y id_dest: $id_dest actualizado",
                                   "data" => "cd_cli: $cd_cli",
                              ];
                         } else {
                              return [
                                   "value" => 0,
                                   "message" => "Se actualizo el cliente, pero  no se creo direccion. El id_dest : $id_dest no existe, por favor creelo",
                              ];
                         }
                    }

                    return [
                         "value" => 2,
                         "message" => "Cliente con id_cli: $id_cli actualizado",
                         "data" => "cd_cli: $cd_cli",
                    ];
               } else {
                    return [
                         "value" => 0,
                         "message" => "Error el usuario con $id_cli no existe",
                    ];
               }
          } catch (\Throwable $th) {
               return [
                    "value" => 0,
                    "message" => "Error en la actualizacion de client, error: $th",
               ];
          }
     }
     private function getCdCliWithUserIdSap($user_id, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $results = $wpdb->get_results("SELECT cd_cli FROM wp_userssap WHERE user_id = $user_id LIMIT 1");
          return $results[0]->cd_cli;
     }
     private function getCd_CliSap($user_id, $data = [], $id_soc)
     {
          // $wpdb = $this->getWPDB($data["id_soc"]);
          $wpdb = $this->getWPDB($id_soc);
          $fecha_actual = date("Y-m-d H:i:s");;
          /* creacion */
          $sql = "INSERT INTO wp_userssap (user_id , cod , date_created) VALUES ($user_id,0,%s)";
          $wpdb->query($wpdb->prepare($sql, $fecha_actual));
          $wpdb->flush();
          $results = $wpdb->get_results("SELECT cd_cli FROM wp_userssap WHERE user_id = $user_id LIMIT 1");
          return $results[0]->cd_cli;
     }
     private function updatePFXFieldsClient($user_id, $data, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "UPDATE wp_userssap SET cod = 1 WHERE user_id = $user_id";
          $wpdb->query($wpdb->prepare($sql));
          $wpdb->flush();
          /*  */
          $IdsAndDataUpdated = $this->mfGetDataPFXFields($id_soc, $data);
          /* verificacion  e insersacion si hay datos de este cliente */
          $dataVerify = $wpdb->get_results("SELECT * FROM wp_prflxtrflds_user_field_data WHERE user_id=$user_id");
          $wpdb->flush();
          if (count($dataVerify) !== 0) {
               /* si hay datos */
               foreach ($dataVerify as $key => $value) {
                    foreach ($IdsAndDataUpdated as $keyf => $fieldcurrent) {
                         if ($value->field_id == intval($fieldcurrent["id"])) {
                              $id_field = intval($fieldcurrent["id"]);
                              $sql1 = "UPDATE wp_prflxtrflds_user_field_data SET user_value = %s WHERE user_id =$user_id AND field_id=$id_field";
                              $result = $wpdb->query($wpdb->prepare($sql1, $fieldcurrent["update"]));
                              if (!$result) new Error("Error en la actualizacion de  datos");
                              $wpdb->flush();
                         }
                    }
               }
               foreach ($IdsAndDataUpdated as $key => $fieldUpdated) {
                    if (!$this->isCreated($fieldUpdated["id"], $dataVerify)) {
                         // print_r(["msg" => "voy a crear datos"]);
                         //si no esta creado lo voy crear
                         $id_field = intval($fieldUpdated["id"]);
                         $sql2 = "INSERT INTO wp_prflxtrflds_user_field_data (field_id,user_id,user_value) VALUES ($id_field,$user_id,%s) ";
                         $wpdb->query($wpdb->prepare($sql2, $fieldUpdated["update"]));
                         $wpdb->flush();
                    }
               }
          } else {
               //crea los campos
               $this->createPFXFieldsClient($user_id, $data, $id_soc);
          }
     }

     private function createPFXFieldsClient($user_id, $data, $id_soc)
     {
          $IdsAndDataUpdated = $this->mfGetDataPFXFields($id_soc, $data);
          $wpdb = $this->getWPDB($id_soc);

          foreach ($IdsAndDataUpdated as $keyfield => $field) {
               $id_field = $field["id"];
               $sql = "INSERT INTO wp_prflxtrflds_user_field_data (field_id,user_id,user_value) VALUES ($id_field,$user_id,%s) ";
               $wpdb->query($wpdb->prepare($sql, $field["update"]));
          }
     }

     private function getUserIDForId_cli($id_cli, $id_soc)
     {
          $datafields = $this->mfGetDataPFXFields($id_soc, ["id_cli" => "1"]);
          $id_field = $datafields[0]["id"];
          $wpdb = $this->getWPDB($id_soc);
          $sql = "SELECT user_id FROM wp_prflxtrflds_user_field_data WHERE user_value=%s AND field_id=$id_field";
          $data = $wpdb->get_results($wpdb->prepare($sql, $id_cli));
          return $data[0]->user_id;
     }
     private function getUserIDByEmail($email, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "SELECT * FROM wp_users WHERE user_email=%s LIMIT 1";
          $data = $wpdb->get_results($wpdb->prepare($sql, $email));
          return $data[0]->ID;
     }

     private function verifyEmail($email, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $results = $wpdb->get_results($wpdb->prepare("SELECT user_email FROM wp_users WHERE user_email= %s LIMIT 1", $email));
          return count($results) == 0 ? true : false;
     }

     private function verifyId_cli($id_cli, $id_soc)
     {
          $datafields = $this->mfGetDataPFXFields($id_soc, ["id_cli" => "1"]);
          $id_field = $datafields[0]["id"];
          $wpdb = $this->getWPDB($id_soc);
          $sql = "SELECT user_value FROM wp_prflxtrflds_user_field_data WHERE user_value=%s AND field_id=$id_field";
          $results = $wpdb->get_results($wpdb->prepare($sql, $id_cli));
          return count($results) == 0 ? true : false;
     }
     private function getValueProfileExtraFields($field_name, $user_id, $id_soc)
     {
          $datafields = $this->mfGetDataPFXFields($id_soc, [$field_name => "1"]);
          $id_field = $datafields[0]["id"];
          $wpdb = $this->getWPDB($id_soc);
          $sql = "SELECT user_value FROM wp_prflxtrflds_user_field_data WHERE field_id=$id_field AND user_id=$user_id LIMIT 1";
          $results = $wpdb->get_results($wpdb->prepare($sql));
          return $results[0]->user_value;
     }



     private function createAddressSoap($user_id, $params, $update = false)
     {
          $id_dest = $params["id_dest"];
          $first_name = $params["first_name"];
          $last_name = $params["first_name"];
          $company = $params["company"];
          $country = $params["country"];
          $address_1 = $params["address_1"];
          $address_2 = $params["address_2"];
          $postcode = $params["postcode"];
          $phone = $params["phone"];
          $email = $params["email"];
          $curl = curl_init();
          //este endpoint esta en maxwoocommerce (plugin) en precor
          curl_setopt_array($curl, array(
               CURLOPT_URL => "{$this->PRECOR_URL}/wp-json/max_functions/v1/address",
               // CURLOPT_URL => "http://precor.punkurhr.test/wp-json/max_functions/v1/address",
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => "",
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 0,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => $update == true ? "PUT" : "POST",
               CURLOPT_POSTFIELDS => "{\r\n    \"user_id\": \"$user_id\",\r\n    \"security\": {\r\n        \"user\": \"admin\",\r\n        \"pass\": \"admin999\"\r\n    },\r\n    \"data\": {\r\n        \"id_dest\": \"$id_dest\",\r\n        \"first_name\": \"$first_name\",\r\n        \"last_name\": \"$last_name\",\r\n        \"company\": \"$company\",\r\n        \"country\": \"$country\",\r\n        \"address_1\": \"$address_1\",\r\n        \"address_2\":$address_2\"\",\r\n        \"postcode\": \"$postcode\",\r\n        \"phone\": \"$phone\",\r\n        \"email\": \"$email\"\r\n    }\r\n}",
               CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json"
               ),
          ));

          $response = curl_exec($curl);

          curl_close($curl);
          $response = json_decode($response, true);
          return $response["status"] == 200 ? true : false;
     }


     private function getClientsByDate($id_soc, $fecini, $fecfin)
     {
          //example : 2020-11-20 - 2020-11-21
          $response = [];
          $wpdb = $this->getWPDB($id_soc);
          $sql = "SELECT s.cd_cli,u.user_email as email,s.cod,u.display_name as nomb,s.user_id as user_id FROM wp_userssap s INNER JOIN wp_users u ON s.user_id=u.id WHERE s.date_created BETWEEN  %s AND  %s  ORDER BY s.date_created ASC";
          $dataSap = $wpdb->get_results($wpdb->prepare($sql, $fecini, $fecfin));
          $primerClient = "";
          $ORS = "";
          foreach ($dataSap as $key => $clientSap) {
               if ($key == 0) {
                    $primerClient = $clientSap;
               } else {
                    $ORS .= " OR fields LIKE '%$clientSap->user_email%' ";
               }
          }
          $sql2 = "SELECT fields FROM wp_wpforms_entries WHERE fields LIKE %s $ORS ORDER BY date ASC";
          $dataWPFORM = $wpdb->get_results($wpdb->prepare($sql2, "%$primerClient->user_email%"));
          $clientsData = [];
          foreach ($dataWPFORM as $key2 => $form) {
               $datos = [];
               $observaciones = "";
               $arraytemp = maybe_serialize($form->fields);
               $arraytemp = json_decode($arraytemp, true);
               for ($i = 0; $i < 30; $i++) {
                    $temp = $arraytemp[$i];
                    if ($temp["name"] == "Correo electrónico") {
                         $datos["email"] = $temp["value"];
                    }
                    /* nro documento */
                    if ($temp["name"] == "RUC") {
                         $datos["nrdoc"] = $temp["value"];
                    }
                    if ($temp["name"] == "Teléfono de contacto") {
                         $datos["telf"] = $temp["value"];
                    }
                    /* city - dstr - codubig */
                    if ($temp["name"] == "Tu ciudad") {
                         $city = explode("-", $temp["value"]);
                         $datos["city"] = $city[0] . " - " . $city[1];
                         $datos["distr"] = $city[2];
                         $datos["codubig"] = $city[3];
                    }
                    // /* drcdest */
                    if ($temp["name"] == "Tu dirección") {
                         $datos["drcfisc"] = $temp["value"];
                    }

                    /* observaciones */
                    if ($temp["name"] == "Giro de negocio" || $temp["name"] == "Dinos si eres" || $temp["name"] == "Tu moneda de facturación") {
                         $observaciones .= $temp["name"] . ": " . $temp["value"] . "|";
                    }
               }
               $datos["obs"] = substr($observaciones, 0, -1);
               array_push($clientsData, $datos);
          }
          foreach ($dataSap as $key => $dSap) {
               foreach ($clientsData as $keyo => $client) {
                    if ($dSap->email == $client["email"]) {
                         $dSap->nrdoc = $client["nrdoc"];
                         $dSap->telf = $client["telf"];
                         $dSap->drcfisc = $client["drcfisc"];
                         $dSap->city = $client["city"];
                         $dSap->distr = $client["distr"];
                         $dSap->codubig = $client["codubig"];
                         $dSap->obs = $client["obs"];
                         //obtengo valor del data profile extra fields
                    }
               }
          };

          foreach ($dataSap as $key => $obj) {
               $array = [];
               if ($this->isMaxco($id_soc)) {
                    // cambiar aqui el id soc si es maxco
                    $array["id_soc"] = "EM01";
               } else {
                    $array["id_soc"] = "PR01";
               }
               // $array["id_soc"] = ;
               $array["cd_cli"] = $obj->cd_cli;
               $array["nrdoc"] = $obj->nrdoc;
               $array["nomb"] = $obj->nomb;
               $array["telf"] = $obj->telf;
               $array["drcfisc"] = $obj->drcfisc;
               $array["email"] = $obj->email;
               $array["city"] = $obj->city;
               $array["distr"] = $obj->distr;
               $array["codubig"] = $obj->codubig;
               $array["obs"] = $obj->obs;
               $array["cod"] = $obj->cod;
               //profile extra fields               
               $array["telfmov"] = $this->getValueProfileExtraFields("telfmov", $obj->user_id, $id_soc);

               // META VALUES
               $array["cond_pago"] = $this->getMetaValueByKey("cond_pago", $obj->user_id, $id_soc);
               $array["descrip_cond_pago"] = $this->getMetaValueByKey("descrip_cond_pago", $obj->user_id, $id_soc);

               array_push($response, new Client($array));
          }
          return $response;
     }
     private function getMetaValueByKey($key, $user_id, $id_soc): string
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "SELECT wu.meta_value FROM wp_usermeta wu WHERE wu.user_id = $user_id AND wu.meta_key = %s LIMIT 1";
          $results = $wpdb->get_results($wpdb->prepare($sql, $key));
          $wpdb->flush();
          return $results[0]->meta_value == null ? "" : $results[0]->meta_value;
     }
     private function updateMetaValueByKey($key, $meta_value, $user_id, $id_soc): void
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "UPDATE wp_usermeta  SET meta_value =%s WHERE meta_key = %s AND user_id = $user_id";
          $wpdb->query($wpdb->prepare($sql,  $meta_value, $key));
          $wpdb->flush();
     }
     private function createMetaValueByKey($key, $meta_value, $user_id, $id_soc): void
     {
          $meta_value = $meta_value != null ? $meta_value : "";
          $wpdb = $this->getWPDB($id_soc);
          $sql = "INSERT INTO wp_usermeta (user_id,meta_key,meta_value) VALUES ($user_id,%s,%s) ";
          $wpdb->query($wpdb->prepare($sql, $key, $meta_value));
          $wpdb->flush();
     }
     /*  Fin Clientes */

     /* Creditos */
     public function updateCreditoWoo($credito)
     {
          $id_soc = $credito["id_soc"];
          // $cd_cli = $credito["cd_cli"];
          $id_cli = $credito["id_cli"];
          $mntdisp = $credito["mntdisp"];
          $fvenc = $credito["fvenc"];
          $wallet_status = $credito["status"];
          if ($credito["status"] != null) {
               $wallet_status = $credito["status"] == 1 ? "unlocked" : "locked";
          }
          // wallet_comentario asi se llama el campo para el profile extrafields 
          $wallet_comentario = $credito["wallet_comentario"] ? $credito["wallet_comentario"]  : "";
          $clase_riesgo = $credito["clase_riesgo"] ? $credito["clase_riesgo"]  : "";
          if ($this->isMaxco($id_soc) ||  $this->isPrecor($id_soc)) {
               // $id_soc = 999;
               // $fecha_actual = strtotime(date("Y-m-d", time()));
               // $fecha_entrada = strtotime($fvenc);
               // // si la fecha ingresa es menor que la fecha de hoy
               // if (!$fecha_actual >= $fecha_entrada) {
               //      return [
               //           "value" => 0,
               //           "message" => "No puedes enviar esa fecha: $fvenc es menor que el dia de hoy",
               //      ];
               // }
               try {
                    $user_id = $this->getUserIDForId_cli($id_cli, $id_soc);
                    // $user_id = 3;
                    if ($user_id != null) {
                         $this->mfUpdateFieldsCredito($id_soc, $user_id, $credito, $mntdisp) ? true : new
                              Error();
                         $error = $this->mfUpdateStatusCreditoByUserID($wallet_status, $wallet_comentario, $user_id, $id_soc);
                         if ($error) {
                              return [
                                   "value" => 2,
                                   "message" => "Credito con el id_cli: $id_cli actualizado",
                                   "data" => "Monto Disponible: " . $mntdisp
                              ];
                         } else {
                              return [
                                   "value" => 0,
                                   "message" => "Error al actualizar estado del credito",
                              ];
                         }
                    } else {
                         return [
                              "value" => 0,
                              "message" => "No existe el usuario con id_cli: $id_cli",
                         ];
                    }
               } catch (\Throwable $th) {
                    return [
                         "value" => 0,
                         "message" => "El Credito  con el id_cli: $id_cli no existe error:$th",
                    ];
               }
          } else {
               return [
                    "value" => 0,
                    "message" => "El id_soc: $id_soc no coincide con nuestra sociedad",
               ];
          }
     }

     private function mfUpdateStatusCreditoByUserID($status, $comment, $user_id, $id_soc): bool
     {
          if ($status != null) {
               $wpdb = $this->getWPDB($id_soc);
               $sql = "UPDATE wp_fswcwallet SET status =%s, lock_message=%s WHERE user_id=$user_id";
               return $wpdb->query($wpdb->prepare($sql, $status, $comment));
          }
          return true;
     }
     private function mfUpdateFieldsCredito($id_soc, $user_id, $fields_data, $mntdisp)
     {
          try {
               $data = $this->mfGetDataPFXFields($id_soc, $fields_data);
               // $wpdb = $this->getWPDB($id_soc);
               $wpdb = $this->getWPDB($id_soc);
               //UPDATE wp_prflxtrflds_user_field_data SET user_value = "1111111" WHERE user_id = 8 AND field_id=2;
               /* update profile fields */
               $dataVerify = $wpdb->get_results("SELECT * FROM wp_prflxtrflds_user_field_data WHERE user_id=$user_id");
               $wpdb->flush();
               if (count($dataVerify) !== 0) {
                    foreach ($dataVerify as $key2 => $valued) {
                         foreach ($data as $key => $value) {
                              if ($valued->field_id == $value["id"]) {
                                   $id = $value["id"];
                                   $update = $value["update"];
                                   $sql = "UPDATE wp_prflxtrflds_user_field_data SET user_value = %s WHERE user_id = $user_id AND field_id=$id";
                                   $result = $wpdb->query($wpdb->prepare($sql, $update));
                                   $wpdb->flush();
                                   if (!$result) new Error("Error en la actualizacion de  datos");
                              }
                         }
                    }
               }
               //en esta parte si esos campos no son creados aqui los crea
               foreach ($data as $key => $fieldUpdated) {
                    if (!$this->isCreated($fieldUpdated["id"], $dataVerify)) {
                         $id_field = $fieldUpdated["id"];
                         $sql = "INSERT INTO wp_prflxtrflds_user_field_data (field_id,user_id,user_value) VALUES ($id_field,$user_id,%s) ";
                         $wpdb->query($wpdb->prepare($sql, $fieldUpdated["update"]));
                         $wpdb->flush();
                    }
               }


               /* update wallet balancec aqui es necesario crear estos campos*/
               if (!$this->haveCredits($user_id, $id_soc)) {
                    $this->createAndUpdateCredits($user_id, $mntdisp, 0, $id_soc);
               } else {
                    $this->createAndUpdateCredits($user_id, $mntdisp, 1, $id_soc);
               }
               return true;
          } catch (\Throwable $th) {
               return false;
          }
     }
     private function mfGetDataPFXFields($id_soc, $fields_data)
     {
          $fields_filtered = [];
          // $wpdb = $this->getWPDB($id_soc);
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
     /* funcion que verifica si los fields existen en determinada data */
     private function isCreated($id_field, $dataCreated = [])
     {
          foreach ($dataCreated as $key => $value) {
               if ($value->field_id == intval($id_field)) {
                    return true;
               }
               return false;
          }
     }

     private function haveCredits($user_id, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $results = $wpdb->get_results("SELECT * FROM wp_fswcwallet WHERE user_id=$user_id");
          return count($results) > 0 ? true : false;
     }
     private function createAndUpdateCredits($user_id, $mntdisp, $cod, $id_soc)
     {
          // date_default_timezone_set('America/Lima');
          $wpdb = $this->getWPDB($id_soc);
          $fecha_actual = date("Y-m-d H:i:s");
          if (intval($cod) == 0) {
               //crear credito
               $sqlwallet = "INSERT INTO wp_fswcwallet (user_id,balance,last_deposit,total_spent,status,lock_message) VALUES($user_id,%s,%s,%s,%s,%s)";
               $resultw = $wpdb->query($wpdb->prepare($sqlwallet, $mntdisp, $fecha_actual, 0, "unlocked", "1"));
               $wpdb->flush();
               if (!$resultw) new Error("Error en la creacion de  creditos");
          } else if (intval($cod) == 1) {
               /* actualizacion */
               $sqlwallet = "UPDATE wp_fswcwallet SET balance = %s,last_deposit=%s,total_spent=%s,status=%s,lock_message=%s WHERE user_id = $user_id";
               $resultw = $wpdb->query($wpdb->prepare($sqlwallet, $mntdisp, $fecha_actual, 0, "unlocked", "1"));
               $wpdb->flush();
               if (!$resultw) new Error("Error en la actualizacion de  datos");
          }
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

     private function getUserIDbyCdCli($cd_cli, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $results = $wpdb->get_results("SELECT user_id FROM wp_userssap WHERE cd_cli = $cd_cli LIMIT 1");
          return $results[0]->user_id;
     }
     /* cotizaciones */
     public function GetQuoteWoo($params)
     {
          $id_soc = $params["id_soc"];
          $cd_cli = $params["cd_cli"];
          $id_cli = $params["id_cli"];
          $fcre = $params["fcre"];
          $cod = $params["cod"];

          if ($id_soc == $this->isMaxco($id_soc) || $id_soc == $this->isPrecor($id_soc)) {

               $idOrders = null;
               $user_id = null;
               $user_id = $this->getUserIDbyCdCli($cd_cli, $id_soc);
               // esta funcion ejecutara un query a wp_cotizciones dependiendo si el usuario fue enviado
               $idOrders = $this->existingUserQuotes($user_id, $fcre, $cod, $id_soc);
               if ($idOrders != null) {
                    // // actualiza a enviado (send = 1 )las cotizaciones recibidas
                    foreach ($idOrders as $idOrder) {
                         // a cada cotizacion la pongo ya enviada
                         $this->changeSendQuote($idOrder->id_order, $id_soc);
                    }

                    $quotes = $this->GetFormattedQuotes($idOrders, $cd_cli, $cod, $id_soc);
                    //todo positivo
                    if ($id_cli != null) {
                         return [
                              "value" => 1,
                              "message" => "Cotizaciones con $id_cli en la fecha: $fcre",
                              "data" => $quotes
                         ];
                    } else {
                         return [
                              "value" => 1,
                              "message" => "Cotizaciones en la fecha: $fcre",
                              "data" => $quotes
                         ];
                    }
               } else {
                    if ($id_cli != null) {
                         return [
                              "value" => 0,
                              "message" => "No hay cotizaciones nuevas sin leer del ID_CLI: $id_cli en la fecha: $fcre, no hay idOrders",
                         ];
                    } else {
                         return [
                              "value" => 0,
                              "message" => "No hay cotizaciones nuevas sin leer en la fecha: $fcre, no hay idOrders",
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
     public function GetQuoteStatusWoo($params)
     {
          $id_soc = $params["id_soc"];
          $id_order = $params["id_ctwb"];

          if ($id_soc == $this->isMaxco($id_soc) || $id_soc == $this->isPrecor($id_soc)) {
               // $id_soc = 999;
               try {
                    $orderData = $this->GetStatusQuote($id_order, $id_soc);
                    return [
                         "value" => 1,
                         "message" => "Cotizacion con id_ctwb: $id_order",
                         "data" => $orderData,
                    ];
               } catch (\Throwable $th) {
                    return [
                         "value" => 0,
                         "message" => "Cotizacion con id_ctwb: $id_order no existe",
                    ];
               }
          } else {
               return [
                    "value" => 0,
                    "message" => "El id_soc: $id_soc no coincide con nuestra sociedad",
               ];
          }
     }
     public function UpdateQuoteStatusWoo($params)
     {
          function getStatusDescrip($statusCode)
          {
               $estados = [
                    "ywraq-pending" => 1,
                    "ywraq-accepted" => 2,
                    "on-hold" => 3,
                    "on-hold" => 4,
                    "completed" => 5,
               ];
               foreach ($estados as $key => $value) {
                    if ($value == $statusCode) {
                         return $key;
                    }
               }
          }
          $id_soc = $params["id_soc"];
          $id_order = $params["id_ctwb"];
          $stat = $params["stat"];
          $statusCode = intval(explode("-", $stat)[0]);
          if ($id_soc == $this->isMaxco($id_soc) || $id_soc == $this->isPrecor($id_soc)) {
               // $id_soc = 999;
               $status_descrip = getStatusDescrip($statusCode);
               try {
                    $this->getWoocommerce($id_soc)->put("orders/$id_order", [
                         "status" => $status_descrip
                    ]);
                    // $this->changeCodQuote($id_order, $id_soc); //actu+aliza cod a 1
                    return [
                         "value" => 1,
                         "message" => "El estado ha sido actualizado a $stat",
                    ];
               } catch (\Throwable $th) {
                    return [
                         "value" => 0,
                         "message" => "El id_ctwb: $id_order no existe, error : $th",
                    ];
               }
          } else {
               return [
                    "value" => 0,
                    "message" => "El id_soc: $id_soc no coincide con nuestra sociedad",
               ];
          }
     }

     public function PostQuoteWoo($params)
     {
          function getPosBySkuQuote($order, $sku): string
          {
               foreach ($order->line_items as $item) {
                    if ($item->sku == $sku) {
                         return $item->id;
                    }
               }
          }
          function removeMaterialDelivery($materiales): array
          {
               $arrayMaterials = [];
               foreach ($materiales as $material) {
                    if (intval($material->pos) != 0 && intval($material->id_mat) != 99999) {
                         array_push($arrayMaterials, $material);
                    }
               }
               return $arrayMaterials;
          }
          function existsMaterialByQuote($sku, $quote)
          {
               $result = array_search($sku, array_column($quote->line_items, 'sku'));
               return  $result === 0 || $result != false    ? true : false;
               return $result;
          }
          function addMaterialsQuote($quoteMateriales, $materiales)
          {
          }
          $id_soc = $params["id_soc"];
          $pos = $params["pos"];
          $id_order = $params["id_ctwb"];
          $IDSAP = $params["id_ped"];
          $sku = strval(intval($params["id_mat"]));
          $quantity = $params["cant"];
          $prctot = $params["prctot"];
          $materiales = removeMaterialDelivery($params["materiales"]);

          if ($id_soc == $this->isMaxco($id_soc) || $id_soc == $this->isPrecor($id_soc)) {
               try {
                    $quote = (object) $this->getWoocommerce($id_soc)->get("orders/{$id_order}");
                    // getPosBySkuQuote($quote,"428420")
                    return [
                         "value" => 0,
                         // "message" => is_null($materiales[2]) ? "si" : "no",
                         "message" => existsMaterialByQuote("443190333", $quote) ? "exite" : "no existe"
                         // "message" => existsMaterialByQuote("443190", $quote) 
                         // "message" => existsMaterialByQuote("428420", $quote) 
                    ];

                    // no importa en caso lo use yo siempre guarda su id_sap del pedido
                    $this->createOrUpdateWhenExistsMetaValue("id_ped", $IDSAP, $id_order, $id_soc);
                    $data = [];
                    // $this->UpdateQuoteStatusWoo(["id_soc" => $id_soc, "id_ctwb" => $id_order, "stat" => "1-En Cotizacion"]);
                    // $this->notifyUserAboutQuoteByIdOrder($id_order, $id_soc);
                    // return [
                    //      "value" => 1,
                    //      "message" => "La cotizacion no tiene ninguna modificacion",
                    // ];
                    // agrega un nuevo producto
                    $pos = "";
                    if ($this->verifyMaterialSku($sku, $id_soc)) {
                         $data = array(
                              'line_items' => array(array(
                                   'quantity' => $quantity,
                                   'sku' => $sku,
                                   'total' => number_format($prctot / 1.18, 2, ".", ""),
                              ))
                         );
                         $order = (object) $this->getWoocommerce($id_soc)->put("orders/$id_order", $data);
                         // $this->changeCodQuote($id_order, $id_soc);
                         foreach ($order->line_items as  $value) {
                              if ($value->sku == $sku) {
                                   $pos = $value->id;
                              }
                         }
                         // $id_soc = "EM01";
                         // actualiza el estado de una cotizacion a presupuesto pendiente
                         $this->UpdateQuoteStatusWoo(["id_soc" => $id_soc, "id_ctwb" => $id_order, "stat" => "1-En Cotizacion"]);
                         // envio email al cliente notificando la cotizacion
                         // $this->notifyUserAboutQuoteByIdOrder($id_order, $id_soc);
                         return [
                              "value" => 1,
                              "message" => "Se agrego el id_mat:$sku al id_ctwb: $id_order correctamente",
                              "data" => "POS: $pos",
                         ];
                    } else {
                         return [
                              "value" => 0,
                              "message" => "El material con sku: $sku no existe",
                         ];
                    }
                    //actualiza producto
                    $data = array(
                         'line_items' => array(array(
                              'id' => $pos,
                              'quantity' => $quantity,
                              'sku' => $sku,
                              'total' => number_format($prctot / 1.18, 2, ".", ""),
                         ))
                    );
                    $this->getWoocommerce($id_soc)->put("orders/$id_order", $data);
                    // $this->changeCodQuote($id_order, $id_soc);
                    // $id_soc = "EM01";
                    // actualiza el estado de una cotizacion a presupuesto pendiente
                    $this->UpdateQuoteStatusWoo(["id_soc" => $id_soc, "id_ctwb" => $id_order, "stat" => "1-En Cotizacion"]);
                    // envio email al cliente notificando la cotizacion
                    // $this->notifyUserAboutQuoteByIdOrder($id_order, $id_soc);
                    return [
                         "value" => 2,
                         "message" => "El id_ctwb: $id_order se ha actualizado",
                    ];
               } catch (\Throwable $th) {
                    return [
                         "value" => 0,
                         "message" => "El id_ctwb: $id_order no existe error: $th",
                    ];
               }
          } else {
               return [
                    "value" => 0,
                    "message" => "El id_soc: $id_soc no coincide con nuestra sociedad",
               ];
          }
     }

     private function changeCodQuote($id_order, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "UPDATE wp_cotizaciones SET cod = 1 WHERE id_order = $id_order";
          $wpdb->query($sql);
     }
     private function changeSendQuote($id_order, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "UPDATE wp_cotizaciones SET send = 1 WHERE id_order = $id_order";
          $wpdb->query($sql);
     }

     private function existingUserQuotes($user_id, $fcre, $cod, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $partialSQL = $cod == 0 ? " AND send = 0" : "";
          $sql = "";
          // si envio el cd_cli
          if ($user_id != null) {
               // si cod = 0 envia los no enviados previamente
               $sql = "SELECT * FROM wp_cotizaciones WHERE customer_id = $user_id AND DATE_FORMAT(date_created,'%Y-%m-%d') = '$fcre'  $partialSQL";
          } else {
               // no enviaron un usuario
               $sql = "SELECT * FROM wp_cotizaciones WHERE DATE_FORMAT(date_created,'%Y-%m-%d') = '$fcre'  $partialSQL";
          }
          $results = $wpdb->get_results($sql);
          return count($results) == 0 ? null : $results;
     }
     private function GetMetaValuePostByMetaKey($meta_key, $post_id, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "SELECT meta_value FROM wp_postmeta WHERE meta_key=%s AND post_id=$post_id LIMIT 1";
          $result = $wpdb->get_results($wpdb->prepare($sql, $meta_key));
          return strval($result[0]->meta_value);
     }
     private function GetFormattedQuotes($orders, $cd_cli, $tpcotz, $id_soc)
     {
          $woo = $this->getWoocommerce($id_soc);

          function getCodStatusByDescription(int $tpcotz, string $description): int
          {
               // es cotizacion
               $cod_status = 0;
               if ($tpcotz == 0) {
                    switch ($description) {
                         case 'ywraq-new': //nueva cotizacion
                              $cod_status = 0;
                              break;
                         case 'ywraq-pending': // cotizacion pediente aqui el cliente puede aceptar o rechazar
                              $cod_status = 1;
                              break;
                         case 'ywraq-rejected': // cotizacion rechazada
                              $cod_status = 2;
                              break;
                         case 'ywraq-accepted': //este estado es momentaneo se va defrente a -> on-hold
                              $cod_status = 3;
                              break;
                         case 'ywraq-expired': //cotizacion expirada
                              $cod_status = 4;
                              break;
                         case 'on-hold': //cotizacion en espera para ti este es el aceptado
                              $cod_status = 5;
                              break;
                         case 'completed': // cotizacion pagada y completada
                              $cod_status = 6;
                              break;
                         default:
                              break;
                    }
               } else if ($tpcotz == 1) {
                    // es un pedido ordinario
                    switch ($description) {
                         case 'on-hold': // esta en espera aun no se paga del pago
                              $cod_status = 0;
                              break;
                         case 'completed': // la orden a sido pagado y completada
                              $cod_status = 1;
                              break;
                         case 'pending': // este estado es momentaneo ocurre cuando se hace la transaccion
                              $cod_status = 2;
                              break;
                         default:
                              break;
                    }
               }
               return $cod_status;
          }


          $arrayQuotes = [];
          foreach ($orders as  $order) {
               $quote = (object) $woo->get("orders/{$order->id_order}");
               // if ($quote->created_via == "ywraq") {
               $arraymaterials = [];
               foreach ($quote->line_items as  $m) {
                    $unidad = $this->GetMetaValuePostByMetaKey("und", $m->product_id, $id_soc);
                    $und = ($unidad == null) ? "kg" : $unidad;
                    array_push($arraymaterials, new Material($m->id, $m->sku, $m->name, $m->quantity, $und, $m->price, number_format(doubleval($m->total) + doubleval($m->total_tax), 2, ".", "")));
               }
               foreach ($quote->shipping_lines as $delivery) {
                    if ($delivery->total != "0.00") {
                         array_push($arraymaterials, new Material(0, 99999, "Delivery", 0, "", "", number_format(doubleval($delivery->total) + doubleval($delivery->total_tax), 2, ".", "")));
                    }
               }

               $lat = "";
               $long = "";
               // niubiz llega vacia si no hay data
               $obs_niubiz = "";
               foreach ($quote->meta_data as $m) {
                    if ($m->key == "ce_latitud") {
                         $lat = $m->value;
                    }
                    if ($m->key == "ce_longitud") {
                         $long = $m->value;
                    }
                    if ($m->key == "_visanetRetorno") {
                         $obs_niubiz = $m->value;
                    }
               }
               // convierto a json el obsniubiz
               $jsonNiubiz = maybe_unserialize(json_decode($obs_niubiz));
               $objectNiubiz = new Niubiz($jsonNiubiz->dataMap->TRACE_NUMBER, $jsonNiubiz->dataMap->BRAND, $jsonNiubiz->dataMap->STATUS, $obs_niubiz);

               // obtencion de cod_dest

               $codDest = 0;
               if ($this->isPrecor($id_soc)) {
                    $user_id = $quote->customer_id;
                    $user = (object) $woo->get("customers/$user_id");
                    $direcciones = [];
                    // busco en el metada del cliente las direcciones
                    foreach ($user->meta_data as $meta) {
                         if ($meta->key == "fabfw_address") {
                              array_push($direcciones, $meta);
                         }
                    }
                    foreach ($direcciones as $direccion) {
                         if ($direccion->value->address_1 === $quote->billing->address_1) {
                              $codDest = $direccion->value->id_dest == null  ? 0 : $direccion->value->id_dest;
                         }
                    }
               }
               // algunas modificaciones de string
               if ($quote->status == "ywraq-pending") {
                    $quote->status = "pending";
               }
               if ($quote->payment_method == "fsww") {
                    $quote->payment_method = "Mi Credito Precor";
               } else if ($quote->payment_method == "yith-request-a-quote") {
                    $quote->payment_method = "Cotizacion Nueva";
               }

               if ($quote->payment_method_title == "YITH Request a Quote") {
                    $quote->payment_method_title = "Cotizacion Nueva";
               }

               // cuando no envia cd_cli busco cd_cli por customer_id del quote

               if ($cd_cli == null) {
                    $cd_cli = $this->getCdCliWithUserIdSap($quote->customer_id, $id_soc);
               }
               if ($quote->status == "completed") {
                    $tpcotz = 1;
               }
               // fin de busqueda
               array_push(
                    $arrayQuotes,
                    new Cotizacion($order->id_order, $cd_cli, $codDest, $objectNiubiz, $quote->billing->address_1, $quote->billing->postcode, $quote->payment_method, $quote->payment_method_title, $lat, $long, "001-Delivery", $tpcotz, Utilities::getStatusCode($quote->status), Translate::translateStatus($quote->status), number_format($quote->total, 2, ".", ""), $arraymaterials)
               );
               // }
          }
          return $arrayQuotes;
     }
     private function GetStatusQuote($id_order, $id_soc)
     {
          $woo = $this->getWoocommerce($id_soc);
          $quote = (object) $woo->get("orders/$id_order");
          $statusCode = Utilities::getStatusCode($quote->status);
          // obtencion de objeto niubiz
          $obs_niubiz = null;
          foreach ($quote->meta_data as $m) {
               if ($m->key == "_visanetRetorno") {
                    $obs_niubiz = $m->value;
               }
          }
          // convierto a json el obsniubiz
          $jsonNiubiz = maybe_unserialize(json_decode($obs_niubiz));
          $objectNiubiz = new Niubiz($jsonNiubiz->dataMap->TRACE_NUMBER, $jsonNiubiz->dataMap->BRAND, $jsonNiubiz->dataMap->STATUS, $obs_niubiz);

          $quote->status = str_replace("ywraq-", "", $quote->status);
          if ($quote->payment_method_title == "YITH Request a Quote") {
               $quote->payment_method_title = "Nueva Cotizacion";
          }
          return [new CotizacionStatus($statusCode, Translate::translateStatus($quote->status), ($quote->payment_method_title == "") ? "Sin registrar" : $quote->payment_method_title, $objectNiubiz)];
     }
     private function verifyMaterialSku($sku, $id_soc)
     {
          $material = $this->getWoocommerce($id_soc)->get("products", ["sku" => $sku]);
          return (count($material) == 0) ? false : true;
     }

     private function notifyUserAboutQuoteByIdOrder($id_order, $id_soc)
     {
          $domain = $this->isMaxco($id_soc) ? $this->MAXCO_URL : $this->PRECOR_URL;
          $urlViewQuote = "{$domain}mi-cuenta/view-quote/$id_order/";
          $message = "Estimado usuario, su cotización $id_order ha sido mejorada. Puede entrar al portal <a href='$urlViewQuote'>Aqui</a> para visualizarla.";
          $this->sendEmailbyIdOrder($message, $id_order, $id_soc);
     }

     // envia email al cliente creando una nueva nota por el id_order
     private function sendEmailbyIdOrder($message, $id_order, $id_soc)
     {
          $this->getWoocommerce($id_soc)->post("orders/$id_order/notes", [
               "note" => $message,
               "customer_note" => "true"
          ]);
     }
}
