<?php

error_reporting(0);

require_once(__DIR__ . '/WebServices.php');
require_once(__DIR__ . '/wsdl-creator/class.phpwsdl.php');

$soap = PhpWsdl::RunQuickMode(
     array(                                        // All files with WSDL definitions in comments
          'WebServices.php',
     )
);
