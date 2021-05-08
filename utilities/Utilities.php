<?php
class Utilities
{

    // evalucion de estado por grupo
    public static function getStatusCode($status)
    {
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
                $statusCode = 4;
                break;
        }
        foreach ($vencido as $v3) {
            if ($v3 == $status) {
                $statusCode = 5;
                break;
            }
        }

        return $statusCode;
    }
}
