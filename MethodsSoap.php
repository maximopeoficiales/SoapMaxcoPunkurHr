<?php

use Rakit\Validation\Validator;

require "./MethodsWoo.php";
require "./ResponseObject.php";

class MethodsSoap
{
     private function m()
     {
          return new MethodsWoo();
     }
     private function mfSendResponse($response, $message, $data = null)
     {
          return new ResponseObject($response, $message, ($response == 0) ? 400 : 200, $data);
     }
     public function createMaterial($data)
     {
          return  $this->mfValidationGeneralAuth($data, function ($data) {
               $material = $data["material"];
               $validateMaterial = $this->mfValidateMaterialFields($material); //validacion de security
               if ($validateMaterial["validate"]) {
                    $created = $this->m()->CreateMaterialWoo($material);
                    return $this->mfSendResponse($created["value"], $created["message"], $created["data"]);
                    // return $this->mfSendResponse(1, "Todo Correcto");
               } else {
                    return $this->mfSendResponse(0, $validateMaterial["message"]);
               }
          }, ["security" => "required", "material" => "required"]);
     }
     public function updateStockMaterial($data)
     {
          return  $this->mfValidationGeneralAuth($data, function ($data) {
               $material = $data["material"];
               $validateMaterial = $this->mfValidateMaterialUpdateStock($material);
               if ($validateMaterial["validate"]) {
                    $updated = $this->m()->UpdateMaterialStockWoo($material);
                    return $this->mfSendResponse($updated["value"], $updated["message"], $updated["data"]);
                    // return $this->mfSendResponse(1, "Todo Correcto");
               } else {
                    return $this->mfSendResponse(0, $validateMaterial["message"]);
               }
          }, ["security" => "required", "material" => "required"]);
     }
     public function createClients($data)
     {
          return  $this->mfValidationGeneralAuth($data, function ($data) {
               $cliente = $data["cliente"];
               $validateClient = $this->mfValidateClientsFields($cliente);
               if ($validateClient["validate"]) {
                    $updated = $this->m()->UpdateClientWoo($cliente);
                    return $this->mfSendResponse($updated["value"], $updated["message"], $updated["data"]);
                    // return $this->mfSendResponse(1, "Todo Correcto");
               } else {
                    return $this->mfSendResponse(0, $validateClient["message"]);
               }
          }, ["security" => "required", "cliente" => "required"]);
     }

     public function updateCredits($data)
     {
          return  $this->mfValidationGeneralAuth($data, function ($data) {
               $credito = $data["credito"];
               $validateCredito = $this->mfValidateUpdateCredito($credito);
               if ($validateCredito["validate"]) {
                    $updated = $this->m()->UpdateCreditoWoo($credito);
                    // $updated["data"] = $data;
                    return $this->mfSendResponse($updated["value"], $updated["message"], $updated["data"]);
                    // return $this->mfSendResponse(1, "Todo Correcto");
               } else {
                    return $this->mfSendResponse(0, $validateCredito["message"]);
               }
          }, ["security" => "required", "credito" => "required"]);
     }
     /* retornadores de respuestas */
     private function mfIsAuthorized($user, $password)
     {
          if (true) {
               return true;
          } else {
               return false;
          }
     }
     private function mfNotAuthorized()
     {
          return $this->mfSendResponse(0, "Error en la autenticacion");
     }


     /* functions validations */
     private function mfValidationGeneralAuth($data, $function, $validations = [])
     {
          $validateBody = $this->mfValidateDataEmpty($data, $validations); //validacion de data
          if ($validateBody["validate"]) {
               $security = $data["security"];
               $validateSecurity = $this->mfValidateSecurityFields($security); //validacion de security
               if ($validateSecurity["validate"]) {
                    if ($this->mfIsAuthorized($security["user"], $security["pass"])) {
                         return $function($data);
                    } else {
                         return $this->mfNotAuthorized();
                    }
               } else {
                    return $this->mfSendResponse(0, $validateSecurity["message"], null);
               }
          } else {
               return $this->mfSendResponse(0, $validateBody["message"], null);
          }
     }

     private function mfValidateSecurityFields($security)
     {
          return $this->mfUtilityValidator($security, [
               'user'                  => 'required|max:11',
               'pass'              => 'required|max:13',
          ]);
     }
     private function mfValidateDataEmpty($data, $validations)
     {
          $validator = new Validator;
          $validation = $validator->make($data, $validations);
          $validation->validate();
          if ($validation->fails()) {
               // handling errors
               $errors = $validation->errors();
               return ["validate" => false, "message" => $errors->firstOfAll()];
          } else {
               return ["validate" => true];
          }
     }

     /* validations */
     private function mfValidateUpdateCredito($credito)
     {
          $validations = [
               'id_soc'                  => 'required|max:4',
               'cd_cli'                  => 'required|max:10',
               'id_cli'                  => 'required|digits_between:1,10|numeric',
               'mntcred'              => 'required|digits_between:1,10|numeric',
               'mntutil'              => 'required|digits_between:1,10|numeric',
               'mntdisp'              => 'required|digits_between:1,10|numeric',
               'fvenc'              => 'required|max:10|date:Y-m-d',
          ];
          return $this->mfUtilityValidator($credito, $validations);
     }
     private function mfValidateMaterialFields($material)
     {
          $validations = [
               'id_soc'                  => 'required|max:4',
               'id_mat'                  => 'required|max:12',
               'nomb'              => 'required|max:40',
               'paq'              => 'max:1',
               'undpaq'              => 'max:3',
               'und'              => 'required|max:3',
               'paqxun'              => 'digits_between:1,3|numeric',
               'unxpaq'              => 'numeric',
               'peso'              => 'required|max:7',
               'jprod'              => 'required|max:20',
               'cod'              => 'required|max:1|numeric|in:0,1',
          ];
          return $this->mfUtilityValidator($material, $validations);
     }
     private function mfValidateMaterialUpdateStock($material)
     {
          $validations = [
               'id_soc'                  =>  'required|max:4',
               'id_mat'                  => 'required|max:12',
               'und'              => 'required|max:3',
               'undpaq'              => 'max:3',
               'stck'              => 'required|numeric',
          ];
          return $this->mfUtilityValidator($material, $validations);
     }

     private function mfValidateClientsFields($client)
     {
          $validations = [
               'id_soc'                  =>  'required|max:4',
               'id_cli'                  => 'required|numeric|digits_between:1,10',
               'categ'                  => 'max:10',
               'nomb'                  => 'required|max:40',
               'nrdoc'                  => 'required|max:11',
               'telf'                  => 'max:9',
               'email'                  => 'max:30|email',
               'drcfisc'                  => 'max:70',
               'id_eje'                  => 'numeric|digits_between:1,10',
               'nombeje'                  => 'max:40',
               'telf_eje'                  => 'max:9',
               'email_eje'                  => 'max:30|email',
               'id_dest'              => 'numeric|digits_between:1,10',
               'drcdest'              => 'required|max:70',
               'cod'              => 'required|max:1|numeric|in:0,1',
          ];
          return $this->mfUtilityValidator($client, $validations);
     }
     private function mfUtilityValidator($params, $validations)
     {
          $validator = new Validator;
          $validation = $validator->make($params, $validations);
          $validation->validate();
          if ($validation->fails()) {
               $errors = $validation->errors();
               $text = "";
               foreach ($errors->firstOfAll() as $value) {
                    $text .= strval($value) . ". \n";
               }
               return ["validate" => false, "message" => $text];
          } else {
               return ["validate" => true];
          }
     }
}
