<?php
/**
 * kindleyo.com
 * author lygus
*/
error_reporting(E_ALL ^ E_NOTICE);
header("Content-Type: text/html; charset=UTF-8");
date_default_timezone_set('Asia/Shanghai');

define("DS", '/');
define("CONF_PATH",  dirname(__FILE__).DS.'..'.DS);
define("APP_PATH",  dirname(__FILE__).DS.'..'.DS.'app'.DS);
define("ROOT_PATH",  dirname(__FILE__));

$app = new Yaf_Application(CONF_PATH . "conf/config.ini");
$app->bootstrap()->run();

//Common::accessLog();