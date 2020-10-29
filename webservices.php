<?php
require "./MethodsSoap.php";
class WebServices
{
     private function getMethods()
     {
          return  new MethodsSoap();
     }
     /**
      * Crea Material 
      *
      * @param string $user usuario
      * @param string $pass contraseña
      * @param string $id_soc id de sociedad
      * @param string $id_mat sku de material
      * @param string $cent centro del material
      * @param string $alm almacen del material
      * @param string $nomb nombre del material
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
                    "alm" => $request->alm,
                    "nomb" => $request->nomb,
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
      * Crea Material 
      *
      * @param string $user usuario
      * @param string $pass contraseña
      * @param string $id_soc id de sociedad
      * @param string $id_mat sku de material
      * @param string $cent centro del material
      * @param string $alm almacen del material
      * @param string $und unidad del material
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
                    "alm" => $request->alm,
                    "und" => $request->und,
                    "stck" => $request->stck,
               ],
          ];
          return $m->updateStockMaterial($data);
     }
}
