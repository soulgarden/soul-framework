<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

abstract class Cookies {

    static protected $is_inited = false;
    static protected $_lifetime;
    static protected $_domain;
    static protected $_path = '/';
    static protected $_secure = false;
    static protected $_httponly = false;
    static protected $_store = array();

    static public function init() {
    
        if (self::$is_inited == true) return;
        
        $conf = Config::load('cookies');
        
        self::$_lifetime = $conf['lifetime'];
        self::$_domain   = $conf['domain'];
        self::$_secure   = $conf['secure'];
        self::$_path     = $conf['path'];
        self::$_httponly = $conf['httponly'];
        
        self::$_store = $_COOKIE; 
        
        self::$is_inited = true;
    }
    
    static public function get($name, $default = null) {
        if (self::$is_inited == false) self::init();
        return (!empty(self::$_store[$name])) ? self::$_store[$name] : $default;
    }
    
    static public function set($name, $value, $expiration = null) {
        if (self::$is_inited == false) self::init();
        if (is_null($expiration)) $expiration = self::$_lifetime;
        return setcookie($name, $value, time()+$expiration, self::$_path, self::$_domain, self::$_secure, self::$_httponly);
    }
    
    static public function del($name) {
        if (self::$is_inited == false) self::init();
		unset($_COOKIE[$name]);
        return self::set($name, NULL, -86400);
	}
}
?>
