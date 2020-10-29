<?php
require "./MethodsSoap.php";
class WebServices
{
     private function getMethods()
     {
          return  new MethodsSoap();
     }
     /**
      * Crea Y Actualiza Material 
      *
      * @param string $user Usuario
      * @param string $pass Contraseña
      * @param string $id_soc Id de sociedad
      * @param string $id_mat SKU de material
      * @param string $cent Codigo de Centro
      * @param string $nomb Nombre del material
      * @param string $paq Paquetizado
      * @param string $undpaq Unidad del Paquete
      * @param string $und unidad del material
      * @param string $jprod jerarquia del producto
      * @param string $peso peso del producto
      * @param string $cod peso del producto
      * @return array Respuesta del Servidor
      */
     public function POST_ACT_MAT($request)
     {
          $m = $this->getMethods();
          $data = [
               "security" => [
                    "user" => $request->user,
                    "pass" => $request->pass,
               ],
               "material" => [
                    "id_soc" => $request->id_soc,
                    "id_mat" => $request->id_mat,
                    "cent" => $request->cent,
                    "nomb" => $request->nomb,
                    "paq" => $request->paq,
                    "undpaq" => $request->undpaq,
                    "und" => $request->und,
                    "jprod" => $request->jprod,
                    "peso" => $request->peso,
                    "cod" => $request->cod,
               ],
          ];
          return $m->createMaterial($data);
          // return $data;
     }
     /**
      * Actualiza Stock y campos extras
      *
      * @param string $user Usuario
      * @param string $pass Contraseña
      * @param string $id_soc Id de sociedad
      * @param string $id_mat SKU de material
      * @param string $cent Centro del material
      * @param string $undpaq Unidad de Paquete
      * @param string $und Unidad del material
      * @param string $stck Stock del material
      * @return array Respuesta del Servidor
      */
     public function POST_ACT_STOCK($request)
     {
          $m = $this->getMethods();
          $data = [
               "security" => [
                    "user" => $request->user,
                    "pass" => $request->pass,
               ],
               "material" => [
                    "id_soc" => $request->id_soc,
                    "id_mat" => $request->id_mat,
                    "cent" => $request->cent,
                    "und" => $request->und,
                    "undpaq" => $request->undpaq,
                    "stck" => $request->stck,
               ],
          ];
          return $m->updateStockMaterial($data);
     }
}
