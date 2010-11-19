<?php
if(!defined('APPATH')) header("HTTP/1.0 404 Not Found");

class DefaultController extends Controller {
    
    public function __construct() {}
    
    public function IndexAction() {

        echo 'hello world';
        
        //$tpl = new Tpl;

        //$tpl->assign('message', 'hello world!');

        //$tpl->display('default.tpl');
    }
}
?>
