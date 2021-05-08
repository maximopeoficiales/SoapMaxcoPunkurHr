<?php
class Translate
{

    /* 
           $pagado = ["completed"];
          $pendiente = ["pending", "ywraq-pending", "processing", "on-hold", "ywraq-rejected", "ywraq-accepted"];
          $vencido = ["ywraq-expired", "cancelled", "failed"]; */

    public static function translateStatus($string): string
    {
        $string = str_replace("ywraq-", "", $string);
        $spanish = "";
        switch ($string) {
            case 'completed':
                $spanish = "completado";
                break;
            case 'pending':
                $spanish = "pendiente";
                break;
            case 'processing':
                $spanish = "procesando";
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
                $spanish = "expirado";
                break;
            case 'cancelled':
                $spanish = "cancelado";
                break;
            case 'failed':
                $spanish = "fallado";
                break;
            default:
                $spanish = $string;
                break;
        }
        return $spanish;
    }
}
