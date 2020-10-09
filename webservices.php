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
     public function POST_ACT_MAT($user, $pass, $id_soc, $id_mat, $cent, $alm, $nomb, $und, $jprod, $peso, $cod)
     {
          $m = $this->getMethods();
          $data = [
               "security" => [
                    "user" => $user,
                    "pass" => $pass,
               ],
               "material" => [
                    "id_soc" => $id_soc,
                    "id_mat" => $id_mat,
                    "cent" => $cent,
                    "alm" => $alm,
                    "nomb" => $nomb,
                    "und" => $und,
                    "jprod" => $jprod,
                    "peso" => $peso,
                    "cod" => $cod,
               ],
          ];
          return $m->createMaterial($data);
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
     public function POST_ACT_STOCK($user, $pass, $id_soc, $id_mat, $cent, $alm, $und, $stck)
     {
          $m = $this->getMethods();
          $data = [
               "security" => [
                    "user" => $user,
                    "pass" => $pass,
               ],
               "material" => [
                    "id_soc" => $id_soc,
                    "id_mat" => $id_mat,
                    "cent" => $cent,
                    "alm" => $alm,
                    "und" => $und,
                    "stck" => $stck,
               ],
          ];
          return $m->updateStockMaterial($data);
     }
}
