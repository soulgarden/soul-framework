<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

class Tpl {
    
    public $_compile_dir;
    
    protected $_vars;
    protected $_temp_dir;
    protected $_class_dir;
    protected $_functions;
    
    public function __construct() {

        $this->_class_dir = dirname(__FILE__);
        
        $this->_temp_dir = APPATH.'views';
        $this->_compile_dir = APPATH.'tmp/compiled';
    }
    
    public function __get($name) {
        return $this->_vars[$name];
    }

    public function __set($name, $value) {
        $this->assign($name, $value);
    }
   
    public function set_compile_dir($dir) {
        $this->_compile_dir = rtrim($dir, "/");
    }

    public function set_temp_dir($dir) {
        $this->_temp_dir = rtrim($dir,"/");
    }
    
    public function assign($name, $value) {
        $this->_vars[$name] = $value;
    }

    public function display($template) {
        echo $this->fetch($template);
    }

    public function fetch($template) {
        
        $system = array();
        $system['server'] = $_SERVER;
        $system['get'] = $_GET;
        $system['post'] = $_POST;
        $system['cookie'] = (!empty($_COOKIE)) ? $_COOKIE : '';
        $system['files'] = $_FILES;
        $system['request'] = $_REQUEST;
        $system['session'] = (!empty($_SESSION)) ? $_SESSION : '';

        $this->_vars[''] = $system;
        
        $tpl = $this->_temp_dir.'/'.$template;
        $ctpl = $this->_compile_dir.'/'.md5($template.'tpl');

        if(!file_exists($tpl)) {
            throw new Exception('Не найден файл шаблона '.$tpl);
        }

        if(filemtime($tpl) > @filemtime($ctpl)) {

            require_once $this->_class_dir.'/TplCompiler.php';

            $compiler = new TplCompiler($this);
        
            $compiled = $compiler->compile(file_get_contents($tpl));

            if(!file_put_contents($ctpl, $compiled)) {
                throw new Exception('Не удается сохранить скомпилированный файл '.$ctpl);
            }
        }

        ob_start();
        require($ctpl);
        return ob_get_clean();
    }
    
}
?>
