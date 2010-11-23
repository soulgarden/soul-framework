<?php
define("START", microtime(true)); 

$app = 'application';
$sys = 'system';

define('APPATH', dirname(__FILE__).DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR);
define('SYSPATH', dirname(__FILE__).DIRECTORY_SEPARATOR.$sys.DIRECTORY_SEPARATOR);

try {
    require_once(SYSPATH.'App.php');
    $app = new App();
    $app->processRequest();
}
catch(Exception $e) {
    exit('<pre>'.$e.'</pre>');
}

printf("<br /> Время: %.6f c", microtime(true)-START); 
