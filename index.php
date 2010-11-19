<?php
//define("START", microtime(true)); 

$app = 'application';
$sys = 'system';

define('APPATH', dirname(__FILE__).DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR);
define('SYSPATH', dirname(__FILE__).DIRECTORY_SEPARATOR.$sys.DIRECTORY_SEPARATOR);

try {
    require_once(SYSPATH.'Loader.php');
    require_once(SYSPATH.'Core.php');
    require_once(APPATH.'bootstrap.php');
    
    Core::init();
}
catch(Exception $e) {
    exit('<pre>'.$e.'</pre>');
}

//printf("<br /> Время: %.6f c", microtime(true)-START); 
