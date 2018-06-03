<?php
#start session
session_start();

#import configs
include 'config/envinroments.php';
include 'config/webservices.php';

// MAC
if($_SERVER['HTTP_HOST'] == MAC_URL) {

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ERROR | E_PARSE);

	define('CONTACT_EMAIL', 'viisouza10@live.com');

	define('SIMPLEPHP_PATH', '/Applications/MAMP/htdocs/SimplePHP/');
	define('SOFTWAREVERSION', 'São Roque ERP');
}

if($_SERVER['HTTP_HOST'] == TEST_URL) {
	#error_reporting(0);
    define('SIMPLEPHP_PATH', '/var/www/sites/SimplePHP/');
    define('SOFTWAREVERSION', 'São Roque ERP');
    define('CONTACT_EMAIL', 'viisouza10@live.com');
}

if(in_array($_SERVER['HTTP_HOST'], $PRODUCTION_URLS)) {
	error_reporting(0);
    define('SIMPLEPHP_PATH', '/var/www/SimplePHP/');
    define('SOFTWAREVERSION', 'São Roque ERP');
    define('CONTACT_EMAIL', 'viisouza10@live.com');
}

define('URL', 'http://'.$_SERVER['HTTP_HOST'].'/');

#import libraries
include SIMPLEPHP_PATH.'app/code/libs/PEAR/PEAR.php';
include SIMPLEPHP_PATH.'app/code/libs/MDB2.php';

#init db connections
include 'config/db.php';

#include SimplePhp
require SIMPLEPHP_PATH.'SimplePHP.php';
?>
