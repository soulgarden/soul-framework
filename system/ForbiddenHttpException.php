<?php
if(!defined('SYSPATH')) header("HTTP/1.0 404 Not Found");

class NotFoundHttpException extends Exception {
    public function __construct($message = null) {
        header("HTTP/1.0 403 Forbidden");
    }
}
?>
