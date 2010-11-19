<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

abstract class Loader {

    static protected $store = array();
    static protected $is_inited = false;
    
    static public function init() {
        
        if (self::$is_inited == true) return;
        
        $files = array_merge(glob(SYSPATH.'*/*.php', GLOB_NOSORT),
                             glob(SYSPATH.'*.php', GLOB_NOSORT),
                             glob(APPATH.'libs/*/*.php', GLOB_NOSORT),
                             glob(APPATH.'libs/*.php', GLOB_NOSORT)
                            );
                            
        foreach ($files as $file) {
            $class = basename($file, '.php');
            self::$store[$class] = $file;
        }
        
        self::$is_inited = true;
    }
    
    static public function auto_load($classname) {

        if (!empty(self::$store[$classname])) {
            require_once(self::$store[$classname]);
            return true;
        }
        else {
            return false;
        }
    }
}
?>
