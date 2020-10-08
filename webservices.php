<?php
require "vendor/autoload.php";
require __DIR__ . "/WSDL.php";
$wsdlGenerator =  new \PHP2WSDL\PHPClass2WSDL("WebServices", "http://maxco.punkuhr.test/webservices/webservices.php");
$wsdlGenerator->generateWSDL();
$wsdlGenerator->save(__DIR__ . "/example.wsdl");
$soap = new SoapServer(__DIR__ . "/example.wsdl");
$soap->setClass('WebServices');
$soap->handle();
