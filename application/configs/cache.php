<?php
if(!defined('APPATH')) header("HTTP/1.0 404 Not Found");
return array('cache_exp' => '0',
             'cache_lib' => 'FileCache',
             'cache_exclude' => array('/^admin([.]*)$/'
                                      )
            );
?>
