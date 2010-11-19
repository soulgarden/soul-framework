<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

abstract class Controller implements ControllerInterface{

    public $request;
    
    public function preAction() {
    
    }
    
    public function postAction() {
    
    }

    public function generateUrl($controller, $action = null, $params = null) {
        return Router::generateUrl($controller, $action, $params);
    }

    public function render() {
        
    }

    public function getRenderedView() {
        
    }

    public function redirect($uri, $code = 302) {
        Request::redirect($uri, $code);
    }
}
?>
