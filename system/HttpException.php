<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

class HttpException extends Exception {
    public function __construct($code = 404) {
        switch($code) {
            case 403:
                header("HTTP/1.0 403 Forbidden");
                break;
            case 500:
                header("HTTP/1.0 500 Internal Server Error");
                break;
            case 503:
                header("HTTP/1.0 10.5.4 503 Service Unavailable");
                break;
            default : 
                header("HTTP/1.0 404 Not Found");
        }
    }
}
?>
