<?php

use Rakit\Validation\Validator;

require "./MethodsWoo.php";
class MethodsSoap
{
     private function m()
     {
          return new MethodsWoo();
     }
     public function createMaterial($data)
     {
          return  $this->mfValidationGeneralAuth($data, function ($data) {
               $material = $data["material"];
               $validateMaterial = $this->mfValidateMaterialFields($material); //validacion de security
               if ($validateMaterial["validate"]) {
                    $created = $this->m()->CreateMaterialWoo($material);
                    return $this->mfSendResponse($created["value"], $created["message"], 200, $created["data"]);
                    // return $this->mfSendResponse(1, "Todo Correcto");
               } else {
                    return $this->mfSendResponse(0, $validateMaterial["message"], 400);
               }
          }, ["security" => "required", "material" => "required"]);
     }

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
          return $this->mfSendResponse(0, "Error en la autenticacion", 400);
     }
     private function mfSendResponse($response, $message, $status = 200, $data = null)
     {
          return array(
               'RESPONSE' => $response,
               'DETAILS' => $message,
               'STATUS' => $status,
               'DATA' => $data,
          );
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
                    return $this->mfSendResponse(0, $validateSecurity["message"], 400, null);
               }
          } else {
               return $this->mfSendResponse(0, $validateBody["message"],  400, null);
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
     private function mfValidateMaterialFields($material)
     {
          $validations = [
               'id_soc'                  => 'required|max:4',
               'id_mat'                  => 'required|max:12',
               'cent'                  => 'required|max:4',
               'alm'                  => 'required|max:4',
               'nomb'              => 'required|max:40',
               'und'              => 'required|max:3',
               'peso'              => 'required|max:6',
               'jprod'              => 'required|max:20',
               'cod'              => 'required|max:1',
          ];
          return $this->mfUtilityValidator($material, $validations);
     }
     private function mfUtilityValidator($params, $validations)
     {
          $validator = new Validator;
          $validation = $validator->make($params, $validations);
          $validation->validate();
          if ($validation->fails()) {
               $errors = $validation->errors();
               return ["validate" => false, "message" => $errors->firstOfAll()];
          } else {
               return ["validate" => true];
          }
     }
}
