<?php
require "./MethodsSoap.php";
class WebServices
{
     protected $events = array(
          1 => array(
               "name" => "Excellent PHP Event",
               "date" => 1409994000,
               "location" => "Amsterdam"
          ),
          2 => array(
               "name" => "Marvellous PHP Conference",
               "date" => 1412672400,
               "location" => "Toronto"
          ),
          3 => array(
               "name" => "Fantastic Community Meetup",
               "date" => 1411894800,
               "location" => "Johannesburg"
          )
     );

     private function getMethods()
     {
          return  new MethodsSoap();
     }
     /**
      * Get all the events we know about
      *
      * @return array The collection of events
      */
     public function getEvents()
     {
          return $this->events;
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
     public function setCreateMaterial($user, $pass, $id_soc, $id_mat, $cent, $alm, $nomb, $und, $jprod, $peso, $cod)
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
      * Fetch the detail for a single event
      *
      * @param int $event_id The identifier of the event
      *
      * @return array The event data
      */
     public function getEventById($event_id)
     {
          if (isset($this->events[$event_id])) {
               return $this->events[$event_id];
          } else {
               throw new Exception("Event not found");
          }
     }
}
