<?php
spl_autoload_register(array('App','autoload'));

class App {

    const version = '0.5';
    static protected $_environment = 'dev';
    protected $_name;
    protected $_webPath;
    static protected $_appClasses = array();
    static protected $_systemClasses = array('BaseException' => 'BaseException.php',
                                             'Config' => 'Config.php',
                                             'Controller' => 'Controller.php',
                                             'ControllerInterface' => 'ControllerInterface.php',
                                             'Cookies' => 'Cookies.php',
                                             'ForbiddenHttpException' => 'ForbiddenHttpException.php',
                                             'HttpException' => 'HttpException.php',
                                             'NativeSessions' => 'NativeSessions.php',
                                             'NotFoundHttpException' => 'NotFoundHttpException.php',
                                             'PhpConfig' => 'PhpConfig.php',
                                             'Router' => 'Router.php',
                                             'Request' => 'Request.php',
                                             'Sessions' => 'Sessions.php',
                                             'SessionsInterface' => 'SessionsInterface.php',
                                             'Tpl' => 'Tpl/Tpl.php');
    
    public function __construct($environment = null, $confType = 'php') {
        $this->setEnvironment($environment);
        Config::setExtension($confType);
        $config = Config::load($this->getEnvironment());
        $this->_name = $config['name'];
        $this->_webPath = $config['webpath'];
        $this->importClasses($config['importClasses']);
        Router::setWebPath($config['webpath']);
    }
    
    static public function getVersion() {
        return self::version;
    }
    
    public function getEnvironment() {
        return self::$_environment;
    }
    
    public function setEnvironment($environment) {
        if ($environment == 'prod') {
            self::$_environment = 'production';
        }
        else {
            self::$_environment = 'development';
        }
    }
    
    public function processRequest() {
        ob_start();
        $segments = Router::parseUrl();
        $request = new Request();
        echo $request->execute($segments);
        ob_end_flush();
    }
    
    public function importClasses(array $appClasses) {
        self::$_appClasses = array_merge(self::$_appClasses, $appClasses);
    }
    
    static public function autoload($className) {
        if (!empty(self::$_systemClasses[$className])) {
            require_once(SYSPATH.self::$_systemClasses[$className]);
            return true;
        }
        elseif(!empty(self::$_appClasses[$className])) {
            require_once(SYSPATH.self::$_appClasses[$className]);
            return true;
        }
        return false;
    }
}
