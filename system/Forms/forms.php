<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

class Forms {

    public $validValues;
    
    protected $_form;
    
    private $_version = "Forms library 1.0 beta";
 
    public function __construct($params) {
    
    $name = (empty($params[0])) ? 'form' : $params[0];
    $method = (empty($params[1])) ? 'post' : $params[1];
    $clear = (!isset($params[2]) || ($params[2] == true)) ? true : false; 
    $action = (empty($params[3])) ? '' : $params[3];
    $debug = (empty($params[4])) ? false : true;

        $this->_form = array('name' => $name,
                             'method' => $method,
                             'enctype' => 'application/x-www-form-urlencoded',
                             'fields' => array(),
                             'action' => $action,
                             'debug' => $debug,
                             'valid' => true,
                             'css' => '',
                             'js' => '',
                             'values' => '',
                             'clear' => $clear
                              );
         
        $this->_form['values'] = ($this->_form['method'] == 'post') ? $_POST : $_GET;       
    }

    public function __get($key) {
        if (!isset($this->$key)) {
            throw new Exception('Forms::'.$key.' не существует.');
        }
        return $this->$key;
    }
    
    public function set($id, $key, $value) {
        $this->_form['fields'][$id][$key] = $value;
    }
    
    public function setAction($value) {
        $this->_form['action'] = $value;
    }
 
    public function setFormCaption($text) {
        $this->_forms['caption'] = $text;
    }

    public function setFormEnctype($enctype) {
        
        if ($enctype == 'file') {
            $this->_form['enctype'] = 'multipart/form-data';
        }
        else {
            $this->_form['enctype'] = 'application/x-www-form-urlencoded';
        }
    }

    public function fields() {
        $this->_form['fields'] =  func_get_args();
    }
    
    public function addField($array) {
        $this->_form['fields'][] = $array;
    }

    public function validate() {

        require_once 'formsvalidator.php';
        $validator = new FormsValidator($this->_form);
        $validator->run();
        $this->_form = $validator->getForm();
    }
    
    public function isValid() {
        if ($this->_form['valid']) {
            return true;
        }
        else {
            return false;
        }
    }
                      
    public function getValues() {
        return $this->_form['values'];
    }

    public function build() {
        
        require_once 'formsbuilder.php';
        $builder = new FormsBuilder($this->_form);
        $builder->run();
        
        $this->_form = $builder->getForm();
        
        return $this->_form['html'];
    }
}
?>
