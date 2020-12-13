<?php
use Phalcon\Mvc\Router\Group;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Loader;

$di->setShared('config', function () {
    $config = include __DIR__ . '/config.php';
    
    return $config;
});

// Set up the view component
$di->setShared('view', function () {
    $config = $this->getConfig();
    $view   = new View();
        
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);
    $view->registerEngines(['.phtml' => PhpEngine::class]);
    return $view;
});
    
$config = $di->getConfig();
$loader = new Loader(); // Use Loader() to autoload
$app    = new Application($di);

$loader->registerDirs(
    [
        $config->application->controllersDir,
    ]
)->register();
            
$g = new Group([
    'controller' => 'index',
    'action'     => 'index'
]);
            
$g->add('/(.+)');
$router->mount($g);
$router->handle($uri_path);
ob_get_clean();
ob_start();
echo str_replace(["\n","\r","\t"], '', $app->handle($uri_path)->getContent());            