<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

abstract class Core {

    static public $env = 'dev';
    static protected $is_inited = false;
    
    static public function setEnvironment($env) {
        if ($env == 'prod') {
            self::$env = 'prod';
        }
    }
    
    static public function init() {
    
        if (self::$is_inited == true) return;
        
        ob_start();
        $segments = Router::parseUrl();
        $request = new Request();
        echo $request->execute($segments);
        ob_end_flush();
        
        self::$is_inited = true;
    }
}
?>
