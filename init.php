<?php

error_reporting(0);

require_once(__DIR__ . '/WebServices.php');
function url_completa($forwarded_host = false)
{
     $ssl   = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
     $proto = strtolower($_SERVER['SERVER_PROTOCOL']);
     $proto = substr($proto, 0, strpos($proto, '/')) . ($ssl ? 's' : '');
     if ($forwarded_host && isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
          $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
     } else {
          if (isset($_SERVER['HTTP_HOST'])) {
               $host = $_SERVER['HTTP_HOST'];
          } else {
               $port = $_SERVER['SERVER_PORT'];
               $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
               $host = $_SERVER['SERVER_NAME'] . $port;
          }
     }
     return $proto . '://' . $host;
}

$options = [
     'soap_version' => SOAP_1_1,
     'cache_wsdl' => WSDL_CACHE_NONE
];
$urld=url_completa();
$server = new SoapServer("$urld/webservices/wsdl.php", $options);
$server->setClass('WebServices');
$server->handle();
