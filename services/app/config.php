<?php
use Phalcon\Config;

return new Config(
    [
        'application' => [
            'baseUri'   => '/',
            'modelsDir' => __DIR__ . '/models/'
        ],
        
        'DB' => [
            'dbname'   => 'u352767641_app',
            'username' => 'u352767641_app',
            'password' => '=!@&13B]d',
            'host'     => '141.136.41.1',
            'charset'  => 'utf8',
            'adapter'  => 'Mysql'
        ],
        
        'api' => [
            'user_url'      => BASE_URL.'/api/auth/v1/user',
            'token_url'     => BASE_URL.'/api/auth/v1/oauth/token/validate',
            'auth_url'      => BASE_URL.'/api/auth/v1/oauth/token',
            'client_id'     => 'general-3426',
            'client_secret' => 'iytds43wert67y8urrdfg6766dfg676drf7'
        ]        
    ]    
);
