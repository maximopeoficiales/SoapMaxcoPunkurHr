<?php
class Utilities
{

    // evalucion de estado por grupo
    public static function getStatusCode($quote, $id_soc)

    {
        $status = $quote->status;
        $paymentMethodTitle = $quote->payment_method_title;
        // $status = $quote->status;
        // data
        $pendiente = [
            "pending", "ywraq-pending", "processing", "on-hold",
            // "ywraq-rejected",
            "ywraq-accepted"
        ];
        $vencido = ["ywraq-expired", "cancelled", "failed"];
        $statusCode = 0;

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

        foreach ($pendiente as $v2) {
            if ($v2 == $status) {
                $statusCode = 1;
                break;
            }
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
                if (
                    $paymentMethodTitle == "BBVA" ||
                    $paymentMethodTitle == "BBVA $" ||
                    $paymentMethodTitle == "BCP" ||
                    $paymentMethodTitle == "BCP $" ||
                    $paymentMethodTitle == "ScotiaBank" ||

                    $paymentMethodTitle == "BCP S/." ||
                    $paymentMethodTitle == "BBVA S/."
                ) {
                    $statusCode = 4;
                }
            }
            // si es maxco no existe aceptado
            if (self::isMaxco($id_soc)) {
                if (
                    $paymentMethodTitle == "BBVA" ||
                    $paymentMethodTitle == "BBVA $" || $paymentMethodTitle == "BCP" ||
                    $paymentMethodTitle == "BCP $" ||
                    $paymentMethodTitle == "ScotiaBank" ||
                    $paymentMethodTitle == "BCP S/." ||
                    $paymentMethodTitle == "BBVA S/."
                ) {
                    $statusCode = 4;
                }
            }



            // // solo con tarjeta de credito
            // if ($paymentMethodTitle == "Pago con tarjeta de crédito") {
            //     $statusCode = 5;
            // }

            // nuevas validaciones de estatus code 06/02/22
            // if (self::isPrecor($id_soc)) {
            if ($paymentMethodTitle == "Mi crédito PRECOR") {
                $statusCode = 5;
            }

            // solo con tarjeta de credito
            if ($paymentMethodTitle == "Pago con tarjeta de crédito") {
                // nuevos codigo de estado cuando es tarjeta de credito
                if ($status == "failed" || $status == "refunded" || $status == "rejected") {
                    $statusCode = 6;
                } else if ($status == "processing") {
                    $statusCode = 7;
                } else if ($status == "completed") {
                    $statusCode = 8;
                } else {
                    $statusCode = 9;
                }
            }
            // }
        }

        return $statusCode;
    }

    private static function isMaxco($id_soc)
    {
        if ($id_soc == "EM01") {
            return true;
        } else if ($id_soc == "MA01") {
            return true;
        } else {
            return false;
        }
    }

    private static function isPrecor($id_soc)
    {
        if ($id_soc == "PR01") {
            return true;
        } else {
            return false;
        }
    }
}
