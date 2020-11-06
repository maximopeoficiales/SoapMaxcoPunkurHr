<?php

error_reporting(0);

require_once(__DIR__ . '/WebServices.php');
// require_once(__DIR__ . '/wsdl-creator/class.phpwsdl.php');

// $soap = PhpWsdl::RunQuickMode(
//      array(                                        // All files with WSDL definitions in comments
//           'WebServices.php',
//      )
// );

$options = [
     'soap_version' => SOAP_1_1,
     'cache_wsdl' => WSDL_CACHE_NONE
];

$server = new SoapServer('http://maxco.punkuhr.test/webservices/wsdl.php', $options);
$server->setClass('WebServices');
$server->handle();
