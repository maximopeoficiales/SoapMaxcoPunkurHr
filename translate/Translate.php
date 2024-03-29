<?php
class Translate
{

    /* 
           $pagado = ["completed"];
          $pendiente = ["pending", "ywraq-pending", "processing", "on-hold", "ywraq-rejected", "ywraq-accepted"];
          $vencido = ["ywraq-expired", "cancelled", "failed"]; */

    public static function translateStatus($quote, $statusCode = null): string
    {

        $status = str_replace("ywraq-", "", $quote->status);
        $spanish = "";
        // $transactionId = null;

        // foreach ($quote->meta_data as $m) {
        //     // esto solo pasa cuando es aceptado se guarda en el metadata
        //     if ($m->key == "Transaction ID") {
        //         $transactionId = intval($m->value);
        //         break;
        //     }
        // }

        switch ($status) {
            case 'completed':
                $spanish = "completado";
                break;
            case 'pending':
                $spanish = "pendiente";
                break;
            case 'processing':
                $spanish = "procesando";
                break;
            case 'on-hold':
                $spanish = "en espera";
                break;
            case 'rejected':
                $spanish = "rechazado";
                break;
            case 'accepted':
                $spanish = "aceptado";
                break;
            case 'expired':
                $spanish = "vencido";
                break;
            case 'cancelled':
                $spanish = "cancelado";
                break;
            case 'failed':
                $spanish = "fallado";
                break;
            default:
                $spanish = $status;
                break;
        }
        // si es recaudacion
        if ($statusCode == 4) {
            $spanish = "recaudacion";
            // pendiente
        } else if ($statusCode == 1) {
            // caso especial cuando es pendiente buscas en el metadata si es aceptado
            foreach ($quote->meta_data as $m) {
                // esto solo pasa cuando es aceptado se guarda en el metadata
                if ($m->key == "ywraq_raq_status") {
                    if ($m->value == "accepted") {
                        $spanish = "aceptado";
                        // break;
                    }
                    if ($m->value == "expired") {
                        $spanish = "vencido";
                        // break;
                    }
                }
            }
            // llega correctamente entonces trae el codigo correcto
        } else if ($statusCode == 2) {
            foreach ($quote->meta_data as $m) {
                // esto solo pasa cuando es aceptado se guarda en el metadata
                if ($m->key == "ywraq_raq_status") {
                    if ($m->value == "accepted") {
                        $spanish = "aceptado";
                        // break;
                    }
                    if ($m->value == "expired") {
                        $spanish = "vencido";
                        // break;
                    }
                }
            }
        } else if ($statusCode == 5) {

            // correcion para correcto funcionamiento del webservices
            $spanish = "completado";
        } else if ($statusCode == 7) {
            $spanish = "pago procesando";
        } else if ($statusCode == 8) {
            $spanish = "pago procesado";
        } else if ($statusCode == 6) {
            $spanish = "pago rechazado";
        }



        return $spanish;
    }
}
