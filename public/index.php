<?php
date_default_timezone_set('UTC');

use Phalcon\Exception;
use Phalcon\Di\FactoryDefault;

ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

define('BASE_URL',         'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST']);
define('PUBLIC_PATH',       __DIR__);
define('BASE_PATH',         dirname(__DIR__));
define('SERVICES_PATH',     BASE_PATH . '/services');
define('AUTH_SERVICE_PATH', SERVICES_PATH . '/auth');
define('APP_SERVICE_PATH',  SERVICES_PATH . '/app');
define('CLIENT_PATH',       BASE_PATH . '/client');

try {
    require BASE_PATH . "/vendor/autoload.php";
    
    $di       = new FactoryDefault();
    $router   = $di->getRouter();
    $uri_path = method_exists($router, 'getRewriteUri') ? $router->getRewriteUri() : $_SERVER["REQUEST_URI"];
    $route    = $router->getMatchedRoute();
    
    if ( is_object($route) ) {
        $pattern = $route->getPattern();
        $pattern = rtrim($pattern, '/ ');
        
    } else {
        $pattern = $uri_path;
    }
    
    if ( preg_match("/^((\/){0,1}api(\/.*)*$)/i", $uri_path) > 0 ) {
        if ( preg_match("/^((\/){0,1}api\/auth(\/.*)*$)/i", $uri_path) > 0 ) {
            require_once AUTH_SERVICE_PATH . '/start.php';
            
        } else {
            require_once APP_SERVICE_PATH . '/start.php';
        }
        
    } else {
        require_once CLIENT_PATH . '/start.php';
    }
    
} catch (Exception $e) {
    echo $e->getMessage() . '<br>';
    //echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
