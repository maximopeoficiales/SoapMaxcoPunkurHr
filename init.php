<?php
define('WP_USE_THEMES', false);
require('../wp-blog-header.php');
require "vendor/autoload.php";
require __DIR__ . "/WebServices.php";
$dominio = get_site_url();
$wsdlGenerator =  new \PHP2WSDL\PHPClass2WSDL("WebServices", "$dominio/webservices/init.php");
$wsdlGenerator->generateWSDL();
$wsdlGenerator->save(__DIR__ . "/example.wsdl");
$soap = new SoapServer(__DIR__ . "/example.wsdl");
$soap->setClass('WebServices');
$soap->handle();
// header("Location: $dominio/wp-json/webservice/wsdl");
