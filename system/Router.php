<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

abstract class Router {

    static protected $_routes = null;

    static public function setRoutes(array $routes) {
        self::$_routes = $routes;
    }

    static public function parseUrl($uri = false) {
        
        if ($uri == false) {
            $offset =  ltrim($_SERVER['SCRIPT_NAME'], '/index.php');
            $uri = ltrim($_SERVER['REQUEST_URI'], $offset);
            $uri = trim($uri, '/');
        }
        
        if (!empty($uri)) {
            if (!is_null(self::$routes)) {
                        
                foreach(self::$routes as $pattern => $route ) {

                    if (preg_match($pattern, $uri)) {
                        $internalRoute = preg_replace($pattern, $route, $uri);
                        return (explode('/', $internalRoute));
                    } 
                }
            }
 
            //url does not match any of the rules
            return (explode('/', $this->_uri));
        }
        else {
            //return main controller
            return (array('default', 'index'));
        }
    }
    
    //TODO: добавить проверку на роуты, hmvc
    static public function generateUrl($controller, $action = null, $params = null) {
        if (is_null($action)) {
            return 'http://'.$_SERVER['HTTP_HOST'].'/'.$controller.'/';
        }
        elseif (is_null($params)) {
            return 'http://'.$_SERVER['HTTP_HOST'].'/'.$controller.'/'.$action.'/';
        }
        
        if (!is_array($params)) {
            return 'http://'.$_SERVER['HTTP_HOST'].'/'.$controller.'/'.$action.'/'.$params.'/';
        }
        
        $str = '';
        foreach ($params as $param) {
            $str .= $param.'/'; 
        }
        return 'http://'.$_SERVER['HTTP_HOST'].'/'.$controller.'/'.$action.'/'.$str;
    }
}
?>
