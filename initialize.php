<?php
require "vendor/autoload.php";
require __DIR__ . "/WebServices.php";
$wsdlGenerator =  new \PHP2WSDL\PHPClass2WSDL("WebServices", "http://maxco.punkuhr.test/webservices/initialize.php");
$wsdlGenerator->generateWSDL();
$wsdlGenerator->save(__DIR__ . "/example.wsdl");
$soap = new SoapServer(__DIR__ . "/example.wsdl");
$soap->setClass('WebServices');
$soap->handle();
// return ["msg" => "wsdl generado y funcionando correctamente."];
