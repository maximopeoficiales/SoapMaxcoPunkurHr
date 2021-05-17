<?php
class Utilities
{

    // evalucion de estado por grupo
    public static function getStatusCode($quote)

    {
        $status = $quote->status;
        $paymentMethodTitle = $quote->payment_method_title;
        // $status = $quote->status;
        // data
        $pendiente = ["pending", "ywraq-pending", "processing", "on-hold", "ywraq-rejected", "ywraq-accepted"];
        $vencido = ["ywraq-expired", "cancelled", "failed"];
        $statusCode = 0;
        foreach ($pendiente as $v2) {
            if ($v2 == $status) {
                $statusCode = 1;
                break;
            }
        }
        // evaludacion de estado simple
        switch ($status) {
            case 'ywraq-accepted':
                $statusCode = 2;
                break;
            case 'ywraq-rejected':
                $statusCode = 3;
                break;
            case 'completed':
                $statusCode = 5;
                break;
        }
        foreach ($vencido as $v3) {
            if ($v3 == $status) {
                $statusCode = 6;
                break;
            }
        }
        // esta en pendiente
        if ($statusCode == 1) {

            // caso especial en woo esta como pending pero en el metdata esta como aceptado
            foreach ($quote->meta_data as $m) {
                // esto solo pasa cuando es aceptado se guarda en el metadata
                if ($m->key == "ywraq_raq_status") {
                    if ($m->value == "accepted") {
                        $statusCode = 2;
                        break;
                    }
                }
            }
            // si el estatus es aceptado y elegia tales metodos de pago es recaudacion
            if ($statusCode == 2) {
                if ($paymentMethodTitle == "BBVA" || $paymentMethodTitle == "BCP" || $paymentMethodTitle == "ScotiaBank") {
                    $statusCode = 4;
                }

            }
        }

        return $statusCode;
    }
}
