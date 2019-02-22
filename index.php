<?php

//debug func
define('ROOT', dirname(__FILE__));
require_once(ROOT.'/application/func/Debug.php');//mb move fthis file to application folder?


require_once(ROOT.'/application/classes/Router.php');
// or 

session_start();

$routes = new Router();
$routes->run();

?>