<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

abstract class Sessions {
    
    static protected $_type = 'native';
    static protected $_obj;
    static protected $is_inited = false;
    
    static public function init() {
    
        if (self::$is_inited == true) return;
        
        $config = Config::get('sessions');
        if (!empty($config['type'])) 
            self::$_type = $config['type'];
            
        $class = ucfirst(self::$_type).'Sessions';
        self::$_obj = new $class;
        
        self::$is_inited = true;
    }
    
    static public function id() {
        if (self::$is_inited == false) self::init();
        return self::$_obj->id();
    }
    
    static public function get($name, $default = null) {
        if (self::$is_inited == false) self::init();
        return self::$_obj->get($name, $default);
    }
    
    static public function set($name, $value) {
        if (self::$is_inited == false) self::init();
        return self::$_obj->set($name, $value);
    }
    
    static public function asArray() {
        if (self::$is_inited == false) self::init();
        return self::$_obj->asArray();
    }
}
?>
