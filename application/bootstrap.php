<?php
if(!defined('APPATH')) header("HTTP/1.0 404 Not Found");

date_default_timezone_set('Europe/Moscow');

setlocale(LC_ALL, 'ru_RU.utf-8');

spl_autoload_register(array('Loader', 'auto_load'));

Core::setEnvironment('dev');

Loader::init();

Config::setExtension('php');

//uncomment it if you want to autostart session
//Sessions::init();

Router::setRoutes(array());
?>
