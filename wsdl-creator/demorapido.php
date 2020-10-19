<?php

error_reporting(0);

require_once('MiClase.php');
require_once('class.phpwsdl.php');
$soap = PhpWsdl::RunQuickMode(
	array(								// All files with WSDL definitions in comments
		'MiClase.php',
	)
);
