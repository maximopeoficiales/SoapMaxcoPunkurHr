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

class MethodsWoo
{
     /* constantes */
     private $PRECOR_URL = "https://precor.punkuhr.com/";
     private $MAXCO_URL = "https://maxco.punkuhr.com/";
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

          if ($this->isMaxco($id_soc)) {
               /* maxco */
               return new wpdb('i5142852_wp4', 'F.L7tJxfhTbrfbpP7Oe41', 'i5142852_wp4', 'localhost');
          } else if ($this->isPrecor($id_soc)) {
               /* precor */
               return new wpdb('i5142852_wp7', 'O.WfNQrZjiDKYtz507j13', 'i5142852_wp7', 'localhost');
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
     /* Materiales */
     public function UpdateMaterialStockWoo($material)
     {
          $id_soc = $material["id_soc"];
          if ($this->isMaxco($id_soc) ||  $this->isPrecor($id_soc)) {
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
          $material["peso"] = $weight;
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
                         "key" => "und_value",
                         "value" =>  $weight,
                    ]
               ];
          }
          $id_soc = $material["id_soc"];
          $woo = $this->getWoocommerce($id_soc);
          $newfields = ["id_soc", "paq", "undpaq", "paqxun", "unxpaq", "jprod", "und"];
          foreach ($this->mfAddNewFieldsMetadata($material, $newfields) as  $value) {
               array_push($dataSend["meta_data"], $value);
          }
          array_push($dataSend["meta_data"], ["key" => "peso", "value" => $weight]); //funcion con acf

          if ($this->isMaxco($id_soc) ||  $this->isPrecor($id_soc)) {
               /* creacion */
               if ($material["cod"] == 0) {
                    try {
                         $response = (object) $woo->post('products', $dataSend); //devuelve un objeto
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
                         $user_ide = $this->mfGetIdMaterialWithSku($sku, $id_soc);
                         $this->mfUpdateMetadataMaterial($user_ide, $dataSend["meta_data"], $id_soc);
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
     public function updateMaterialPrice($material)
     {
          $id_soc = $material["id_soc"];
          $id_mat = $material["id_mat"];
          $price = $material["prec"];
          $categ = $material["categ"];
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
                              $sql = "CALL update_rol_precio($id_material,%s,$price)";
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
                         "message" => "Error: $th",
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
     private function ExistsFieldMaterialMetadata($field, $id_material, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "SELECT * FROM wp_postmeta WHERE post_id=$id_material AND meta_key=%s LIMIT 1";
          $result = $wpdb->get_results($wpdb->prepare($sql, $field));
          // $wpdb->flush();
          return (count($result) !== 0) ? true : false;
     }
     private function createFieldMaterialMetadata($key, $value, $id_material, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "INSERT INTO wp_postmeta (post_id,meta_key,meta_value)  VALUES ($id_material,%s,%s) ";
          $wpdb->query($wpdb->prepare($sql, $key, $value));
          $wpdb->flush();
          return true;
     }
     /* fin de materiales */
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
          $params = array(
               "id_dest" => $cliente["id_dest"],
               "first_name" => $cliente["nomb"],
               "last_name" => "",
               "company" => $cliente["nrdoc"],
               "country" => "PE",
               "address_1" => $cliente["drcdest"],
               "address_2" => "",
               "postcode" => "07001",
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
                    return $this->createCliente($cliente, false);
               } else if ($cod == 1) {
                    return $this->UpdateCliente($cliente, false);
               } else if ($cod == 2) {
                    //solo crea destinatarios
                    $user_id = $this->getUserIDForId_cli($cliente["id_cli"], $id_soc);
                    // linea necesario para que pueda crear destinatarios solo con el email
                    if ($user_id == null) {
                         $user_id = $this->getUserIDByEmail($cliente["email"], $id_soc);
                    }
                    $cd_cli = $this->getCdCliWithUserIdSap($user_id, $id_soc);
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
               } else if ($cod == 3) {

                    /*crea cliente y crea direccion  */
                    return $this->createCliente($cliente, true);
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
                    return $this->UpdateCliente($cliente, true);
               } else {
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
     private function createCliente($cliente, $activeDest = false)
     {
          $id_soc = $cliente["id_soc"];
          $id_cli = $cliente["id_cli"];
          $dataSend = [
               'email' => $cliente["email"],
               'first_name' => $cliente["nomb"],
               'username' => $cliente["email"],
               'password' => "123456789",
               'billing' => [
                    "address_1" => $cliente["drcfisc"] == null ? "" : $cliente["drcfisc"],
                    'email' => $cliente["email"],
                    'phone' => $cliente["telfmov"] == null ? "" : $cliente["telfmov"],
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
                              $id_dest = $cliente["id_dest"];
                              $params = array(
                                   "id_dest" => $cliente["id_dest"],
                                   "first_name" => $cliente["nomb"],
                                   "last_name" => "",
                                   "company" => $cliente["nrdoc"],
                                   "country" => "PE",
                                   "address_1" => $cliente["drcdest"],
                                   "address_2" => "",
                                   "postcode" => "07001",
                                   "phone" => $cliente["telfmov"],
                                   "email" => $cliente["email"]
                              );
                              // $user_id = $this->getUserIDForId_cli($cliente["id_cli"], $id_soc);
                              if ($this->createAddressSoap($response->id, $params)) {
                                   return [
                                        "value" => 1,
                                        "data" => "cd_cli: " .  $cd_cli,
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
                    return [
                         "value" => 1,
                         "data" => "cd_cli: " .  $cd_cli,
                         "message" => "Registro de Cliente Exitoso",
                    ];
               }
          } catch (\Throwable $th) {
               return [
                    "value" => 0,
                    "message" => "El id_cli: $id_cli ya existe, error en la creacion de cliente,
                    
                    los datos ingresados son id_soc: $id_soc" . " email: " . $cliente["email"] . " nomb: " . $cliente["nomb"] . " drcfisc: " . $cliente["drcfisc"] . " telfmov: " . $cliente["telfmov"] . " etc \n" . "
                    error: $th",
               ];
          }
     }
     private function UpdateCliente($cliente, $activeDest = false)
     {
          /* actualizacion */
          $id_soc = $cliente["id_soc"];
          $id_cli = $cliente["id_cli"];
          $dataSend = [
               'email' => $cliente["email"],
               'first_name' => $cliente["nomb"],
               'billing' => [
                    'email' => $cliente["email"],
                    'phone' => $cliente["telfmov"],
                    'address_1' => $cliente["drcfisc"],
                    // 'company' => $cliente["nrdoc"],
                    'postcode' => "15000",
                    'country' => "PE",
               ],
          ];
          try {
               //cuando tenga un id_cli si es null busca por email
               $user_id = $this->getUserIDForId_cli($id_cli, $id_soc);
               if ($user_id == null) {
                    $user_id = $this->getUserIDByEmail($cliente["email"], $id_soc);
               }
               $this->getWoocommerce($id_soc)->put("customers/$user_id", $dataSend); //devuelve un objeto
               $this->updatePFXFieldsClient($user_id,  $cliente, $id_soc);
               $cd_cli = $this->getCdCliWithUserIdSap($user_id, $id_soc);

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
                         "postcode" => "07001",
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
          } catch (\Throwable $th) {
               return [
                    "value" => 0,
                    "message" => "El Cliente con el id_cli: $id_cli no existe",
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
               CURLOPT_URL => "https://precor.punkuhr.com/wp-json/max_functions/v1/address",
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
               array_push($response, new Client($array));
          }
          return $response;
     }
     /*  Fin Clientes */

     /* Creditos */
     public function updateCreditoWoo($credito)
     {
          $id_soc = $credito["id_soc"];
          // $cd_cli = $credito["cd_cli"];
          $id_cli = $credito["id_cli"];
          $mntdisp = $credito["mntdisp"];
          if ($this->isMaxco($id_soc) ||  $this->isPrecor($id_soc)) {
               try {
                    $user_id = $this->getUserIDForId_cli($id_cli, $id_soc);
                    $this->mfUpdateFieldsCredito($id_soc, $user_id, $credito, $mntdisp) ? true : new Error();
                    return [
                         "value" => 2,
                         "message" => "Credito con el id_cli: $id_cli actualizado",
                         "data" => "Monto Disponible: " . $mntdisp
                    ];
               } catch (\Throwable $th) {
                    return [
                         "value" => 0,
                         "message" => "El Credito  con el id_cli: $id_cli no existe",
                    ];
               }
          } else {
               return [
                    "value" => 0,
                    "message" => "El id_soc: $id_soc no coincide con nuestra sociedad",
               ];
          }
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
               // $id_soc = 999;
               $user_id = $this->getUserIDbyCdCli($cd_cli, $id_soc);
               $idOrders = $this->existingUserQuotes($user_id, $fcre, $cod, $id_soc);
               if (!$idOrders == null) {
                    $quotes = $this->GetFormattedQuotes($idOrders, $cd_cli, $cod, $id_soc);
                    if (count($quotes) == 0) {
                         return [
                              "value" => 0,
                              "message" => "No hay cotizaciones  del ID_CLI: $id_cli en la fecha: $fcre"
                         ];
                    } else {
                         //todo positivo
                         $tipoOrden = $cod == 0 ? "Cotizaciones" : "Ordenes";
                         return [
                              "value" => 1,
                              "message" => "$tipoOrden del ID_CLI: $id_cli en la fecha: $fcre",
                              "data" => $quotes
                         ];
                    }
               } else if ($idOrders == null && $cod == 0) {
                    return [
                         "value" => 0,
                         "message" => "No hay cotizaciones del ID_CLI: $id_cli en la fecha: $fcre",
                    ];
               } else if ($idOrders == null && $cod == 1) {
                    return [
                         "value" => 0,
                         "message" => "No hay ordenes del ID_CLI: $id_cli en la fecha: $fcre",
                    ];
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
                         "message" => "El id_ctwb: $id_order no existe",
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

          $id_soc = $params["id_soc"];
          $pos = $params["pos"];
          $cod = $params["cod"];
          $id_order = $params["id_ctwb"];
          $sku = $params["id_mat"];
          $quantity = $params["cant"];
          $prctot = $params["prctot"];

          if ($id_soc == $this->isMaxco($id_soc) || $id_soc == $this->isPrecor($id_soc)) {
               // $id_soc = 999;
               try {
                    $data = [];
                    if ($cod == 0) {
                         return [
                              "value" => 1,
                              "message" => "Haz enviado el cod: $cod",
                         ];
                    } else if ($cod == 1) {
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
                              $order =(object) $this->getWoocommerce($id_soc)->put("orders/$id_order", $data);
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
                              $this->notifyUserAboutQuoteByIdOrder($id_order, $id_soc);
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
                    } else if ($cod == 2) {
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
                         $this->notifyUserAboutQuoteByIdOrder($id_order, $id_soc);
                         return [
                              "value" => 2,
                              "message" => "El id_ctwb: $id_order se ha actualizado",
                         ];
                    }
               } catch (\Throwable $th) {
                    return [
                         "value" => 0,
                         "message" => "El id_ctwb: $id_order no existe",
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

     private function existingUserQuotes($user_id, $fcre, $cod, $id_soc)
     {
          $wpdb = $this->getWPDB($id_soc);
          $sql = "SELECT * FROM wp_cotizaciones WHERE customer_id = $user_id AND DATE_FORMAT(date_created,'%Y-%m-%d') = '$fcre' AND cod=$cod";
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

          function getCodStatusByDescription(int $tpcotz, string $description): int
          {
               // es cotizacion
               $cod_status = 0;
               if ($tpcotz == 0) {
                    switch ($description) {
                         case 'ywraq-new':
                              $cod_status = 0;
                              break;
                         case 'ywraq-pending':
                              $cod_status = 1;
                              break;
                         case 'ywraq-rejected':
                              $cod_status = 2;
                              break;
                         case 'ywraq-accepted':
                              $cod_status = 3;
                              break;
                         case 'ywraq-expired':
                              $cod_status = 4;
                              break;
                         default:
                              break;
                    }
               } else if ($tpcotz == 1) {
                    // es un pedido ordinario
                    switch ($description) {
                         case 'pending':
                              $cod_status = 0;
                              break;
                         case 'completed':
                              $cod_status = 1;
                              break;
                         default:
                              break;
                    }
               }
               return $cod_status;
          }
          $arrayQuotes = [];
          $woo = $this->getWoocommerce($id_soc);
          foreach ($orders as  $order) {
               $quote =(object) $woo->get("orders/$order->id_order");
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

               foreach ($quote->meta_data as $m) {
                    if ($m->key == "ce_latitud") {
                         $lat = $m->value;
                    }
                    if ($m->key == "ce_longitud") {
                         $long = $m->value;
                    }
               }

               array_push(
                    $arrayQuotes,
                    new Cotizacion($order->id_order, $cd_cli, $quote->billing->address_1, $quote->billing->postcode, $lat, $long, "001-Delivery", $tpcotz, getCodStatusByDescription($tpcotz, $quote->status), $quote->status, number_format($quote->total, 2, ".", ""), $arraymaterials)
               );
               // }
          }
          return $arrayQuotes;
     }
     private function GetStatusQuote($id_order, $id_soc)
     {
          $woo = $this->getWoocommerce($id_soc);
          $quote = (object) $woo->get("orders/$id_order");
          $statusCode = 0;
          $pagado = ["completed"];
          $pendiente = ["pending", "ywraq-pending", "processing", "on-hold", "ywraq-rejected", "ywraq-accepted"];
          $vencido = ["ywraq-expired", "cancelled", "failed"];
          foreach ($pagado as $v1) {
               if ($v1 == $quote->status) {
                    $statusCode = 1;
               }
          }
          foreach ($pendiente as $v2) {
               if ($v2 == $quote->status) {
                    $statusCode = 2;
               }
          }
          foreach ($vencido as $v3) {
               if ($v3 == $quote->status) {
                    $statusCode = 3;
               }
          }
          return [new CotizacionStatus($statusCode, $quote->status, ($quote->payment_method_title == "") ? "Sin registrar" : $quote->payment_method_title)];
     }
     private function verifyMaterialSku($sku, $id_soc)
     {
          $material = $this->getWoocommerce($id_soc)->get("products", ["sku" => $sku]);
          return (count($material) == 0) ? false : true;
     }

     private function notifyUserAboutQuoteByIdOrder($id_order, $id_soc)
     {
          $domain = $this->isMaxco($id_soc) ? $this->MAXCO_URL : $this->PRECOR_URL;
          $urlViewQuote = "$domain/mi-cuenta/view-quote/$id_order/";
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
