<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

class NativeSessions implements SessionsInterface{

    protected $_name;
    protected $_lifetime;
    protected $_domain;
    protected $_path = '/';
    protected $_secure = false;
    protected $_httponly = false;
    protected $_store = array();

    public function __construct() {
    
        $sessconf = Config::load('sessions');
        $cookieconf = Config::load('cookies');
        
        $this->_name     = $sessconf['name'];
        $this->_lifetime = $sessconf['lifetime'];
        $this->_domain   = $cookieconf['domain'];
        $this->_secure   = $cookieconf['secure'];
        $this->_path     = $cookieconf['path'];
        $this->_httponly = $cookieconf['httponly'];
        
        session_cache_limiter('private');
        session_set_cookie_params($this->_lifetime, $this->_path, $this->_domain, $this->_secure, $this->_httponly);
        session_name($this->_name);
        session_start();
        
        $this->_store = $_SESSION; 
    }
    
    public function id() {
        return session_id();
    }
    
    public function get($name, $default = null) {
        return (!empty($this->_store[$name])) ? $this->_store[$name] : $default;
    }
    
    public function set($name, $value) {
        return $_SESSION[$name] = $value;
    }
    
    public function asArray() {
        return $this->_store;
    }
    
    public function destroy() {
        session_destroy();
    }
}
?>
