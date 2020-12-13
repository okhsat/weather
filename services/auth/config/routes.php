<?php

use Phalcon\Mvc\Micro\Collection as RouteHandler;

//version routes
$router = new RouteHandler();
$router->setHandler('App\Controllers\VersionController', true);
$router->get('/', 'index');
$app->mount($router);

//auth routes
$router = new RouteHandler();
$router->setHandler('App\Controllers\AuthController', true);
$router->setPrefix('/api/auth/v1/oauth');
$router->post('/token', 'token');
$router->get('/authorize', 'authorize');
$router->post('/token/validate', 'validate');
$app->mount($router);

//auth routes
$router = new RouteHandler();
$router->setHandler('App\Controllers\UserController', true);
$router->setPrefix('/api/auth/v1');
$router->get('/user', 'get');
$router->post('/user', 'save');
$router->put('/user', 'save');
$app->mount($router);

