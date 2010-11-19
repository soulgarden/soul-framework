<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

abstract class Config {

    static private $_extension = 'php';

    static public function setExtension($ext) {
        self::$_extension = $ext;
    }
    
    static public function getExtension($ext) {
        return self::$_extension;
    }

    static public function get($name) {
    
        $arr = explode('.', $name);    
        $lib = ucfirst(self::$_extension).'Config';
        
        return $lib::get(array_shift($arr), $arr);
    }

    static public function load($name) {
        $lib = ucfirst(self::$_extension).'Config';
        return $lib::load($name);
    }
}
?>
