<?php
require "./MethodsSoap.php";

class WebServices
{
     private function getMethods()
     {
          return  new MethodsSoap();
     }
     /* Materiales */
     /**
      * Crea Y Actualiza Material 
      *
      * @param string $user Usuario
      * @param string $pass Contraseña
      * @param string $id_soc Id de sociedad
      * @param string $id_mat SKU de material
      * @param string $nomb Nombre del material
      * @param string $paq Paquetizado
      * @param string $undpaq Unidad del Paquete
      * @param string $und unidad del material
      * @param string $paqxun Paquete por unidad
      * @param string $unxpaq Unidad por Paquete
      * @param string $jprod jerarquia del producto
      * @param string $peso peso del producto
      * @param string $cod peso del producto
      * @return array Respuesta del Servidor
      */
     public function POST_ACT_MAT($request)
     {
          $request = $request->request;
          $m = $this->getMethods();
          $data = [
               "security" => [
                    "user" => $request->user,
                    "pass" => $request->pass,
               ],
               "material" => [
                    "id_soc" => $request->id_soc,
                    "id_mat" => $request->id_mat,
                    "nomb" => $request->nomb,
                    "paq" => $request->paq,
                    "undpaq" => $request->undpaq,
                    "und" => $request->und,
                    "paqxun" => $request->paqxun,
                    "unxpaq" => $request->unxpaq,
                    "jprod" => $request->jprod,
                    "peso" => $request->peso,
                    "cod" => $request->cod,
               ],
          ];
          return $m->createMaterial($data);
          // return $request;
     }
     /**
      * Actualiza Stock y campos extras
      *
      * @param string $user Usuario
      * @param string $pass Contraseña
      * @param string $id_soc Id de sociedad
      * @param string $id_mat SKU de material
      * @param string $und Unidad del material
      * @param string $stck Stock del material
      * @return array Respuesta del Servidor
      */
     public function POST_ACT_STOCK($request)
     {
          $request = $request->request;
          $m = $this->getMethods();
          $data = [
               "security" => [
                    "user" => $request->user,
                    "pass" => $request->pass,
               ],
               "material" => [
                    "id_soc" => $request->id_soc,
                    "id_mat" => $request->id_mat,
                    "undpaq" => $request->undpaq,
                    "und" => $request->und,
                    "stck" => $request->stck,
               ],
          ];
          return $m->updateStockMaterial($data);
     }
     /* fin de Materiales */
     /* Clientes */

     /**
      * Crea Y Actualiza CLientes 
      *
      * @param string $user Usuario
      * @param string $pass Contraseña
      * @param string $id_soc Id de sociedad
      * @param int $id_cli Codigo de Cliente
      * @param string $categ Codigo de Categoria
      * @param string $nomb Nombre del material
      * @param string $nrdoc Nro Doc Cliente
      * @param string $telf Telefono Cliente
      * @param string $email Email Cliente
      * @param string $drcfisc Direccion Fiscal
      * @param int $id_eje Codigo Ejecutivo Vendedor
      * @param string $nombreje Nombre de Ejecutivo
      * @param string $telf_eje Telefono de Ejecutivo
      * @param string $email_eje Email de Ejecutivo
      * @param string $email_eje Email de Ejecutivo
      * @param string $id_dest Codigo de Destinatario
      * @param string $drcdest Direccion de Destino
      * @param string $cod Codigo de Actualizacion
      * @return array Respuesta del Servidor
      */
     public function POST_ACT_CLI($request)
     {
          $request = $request->request;
          $m = $this->getMethods();
          $data = [
               "security" => [
                    "user" => $request->user,
                    "pass" => $request->pass,
               ],
               "cliente" => [
                    "id_soc" => $request->id_soc,
                    "id_cli" => $request->id_cli,
                    "categ" => $request->categ,
                    "nomb" => $request->nomb,
                    "nrdoc" => $request->nrdoc,
                    "telf" => $request->telf,
                    "email" => $request->email,
                    "drcfisc" => $request->drcfisc,
                    "id_eje" => $request->id_eje,
                    "nombeje" => $request->nombeje,
                    "telf_eje" => $request->telf_eje,
                    "email_eje" => $request->email_eje,
                    "id_dest" => $request->id_dest,
                    "drcdest" => $request->drcdest,
                    "cod" => $request->cod,
               ],
          ];
          return $m->createClients($data);
     }
     /* Fin de Clientes */

     /* Creditos  */
     /**
      * Actualiza Creditos de un Cliente Especifico
      *
      * @param string $user Usuario
      * @param string $pass Contraseña
      * @param string $id_soc Id de sociedad
      * @param string $cd_cli Correlativo Cliente
      * @param int $id_cli Codigo del Cliente
      * @param string $mnt_cred Monto Credito
      * @param string $mntutil Monto Utilizado
      * @param string $mntdisp Monto Disponible
      * @param string $fvenc Fecha Vencimiento Ejem: 2020-12-30
      * @return array Respuesta del Servidor
      */
     public function POST_ACT_CRED($request)
     {
          $request = $request->request;
          $m = $this->getMethods();
          $data = [
               "security" => [
                    "user" => $request->user,
                    "pass" => $request->pass,
               ],
               "credito" => [
                    "id_soc" => $request->id_soc,
                    "cd_cli" => $request->cd_cli,
                    "id_cli" => $request->id_cli,
                    "mntcred" => $request->mntcred,
                    "mntutil" => $request->mntutil,
                    "mntdisp" => $request->mntdisp,
                    "fvenc" => $request->fvenc,
               ],
          ];
          return $m->updateCredits($data);
     }
     /* Fin de Creditos */
}
