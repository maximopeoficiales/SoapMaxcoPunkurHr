<?php
$server = new SoapServer(null, array(
     'uri' => "http://localhost/php-wsdl-creator-master/MiClase.php"
));
$server->setClass('MiClase');
$server->handle();
