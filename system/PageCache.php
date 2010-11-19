<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

class PageCache {

    private $_uri;
    private $_config;

    public function __construct() {
        $this->_config = Config::get('cache');
        $this->_uri = trim($_SERVER['REQUEST_URI'], '/');
    }

    public function check() {

        $is_cache = true;
 
        if($this->_config['cache_exp'] <= 0) {
            $is_cache = false;
        }
        else {
            if (empty($this->_config['cache_exclude'])) {
                $is_cache = true;    
            }
            else {
                foreach ($this->_config['cache_exclude'] as $exc) {
        
                    if (preg_match($exc, $this->_uri)) {
                        $is_cache = false;
                        break;
                    }
                }
            } 
        }
        
        return $is_cache;
    }
    
    public function is_cache_exists() {
   
        $lib = $this->_config['cache_lib'];
        Loader::lib($lib)->get('page'.$this->_uri);
    }
    
    public function save_cache($cache) {
        $lib = $this->_config['cache_lib'];
        Loader::lib($lib)->set('page'.$this->_uri, $cache);
    } 
}
?>
