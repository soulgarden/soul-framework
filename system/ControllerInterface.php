<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

interface ControllerInterface{

    public function preAction();
    
    public function postAction();

    public function generateUrl($controller, $action = null, $params = null);

    public function render();

    public function getRenderedView();

    public function redirect($uri, $code = 302);
}
?>
