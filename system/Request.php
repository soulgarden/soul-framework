<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

class Request {

    public $controller;
    public $action;
    public $args;

    public function execute(array $segments) {
        
        $controller = ucfirst(array_shift($segments)).'Controller';
        
        if (file_exists(APPATH.'controllers/'.$controller.'.php')) {
            
            require_once(APPATH.'controllers/'.$controller.'.php');
                  
            //если экшена нет, то дефолтный экшен, параметров нет
            if (!isset($segments[0])) {
                $action = 'IndexAction';
                $params = null;
            }
            else {
                $action = ucfirst(array_shift($segments)).'Action';
                        
                //если параметров нет, то пустой массив
                if (!isset($segments[0])) {
                    $params = null;
                }
                else {
                    $params = $segments;    
                }
            }

            //не даст запустить несуществующий экшен
            if(!is_callable(array($controller, $action))) {
                throw new NotFoundHttpException('Page not found');
            }

            $this->controller = $controller;
            $this->action = $action;
            $this->args = $params;
            
            $obj = new $controller($params);
            $obg->request = $this;
            $obj->preAction();
            $response =  $obj->$action($params);
            $obj-> postAction();
            
            return $response;
        }
        else {
            throw new NotFoundHttpException('Page not found');
        }
    }
    
    static public function redirect($uri, $code = 302) {
        if ($code == 302) header("HTTP/1.1 301 Moved Permanently");
        header("Location: $uri");
        exit();
    }
    
    public function getQuery() {
        return $_GET;
    }
    
    public function getQueryParam($key) {
        return $_GET[$key];
    }
    
    public function getResponse() {
        return $_POST;
    }
    
    public function getResponseParam($key) {
        return $_POST[$key];
    } 
}
?>
