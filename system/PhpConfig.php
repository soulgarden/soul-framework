<?php
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
