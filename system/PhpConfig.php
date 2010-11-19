<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

abstract class PhpConfig {

    static public function load($name) {
        return require (APPATH.'configs/'.$name.'.php');
    }
    
    static public function get($name, array $keys) {
        
        $item = self::load($name);
        foreach ($keys as $key) {
             $item = $arr[$key];   
        }
        return $item;
    }
}
?>
