<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

interface SessionsInterface {

    public function id();
    
    public function get($name);
    
    public function set($key, $value);
    
    public function asArray();
    
    public function destroy();
}
?>
