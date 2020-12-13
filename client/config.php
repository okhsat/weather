<?php
use Phalcon\Config;

return new Config(
    [
        'application' => [
            'viewsDir'       => __DIR__ . '/views',
            'controllersDir' => __DIR__
        ],
        
        'api' => [
            'cities_url'    => BASE_URL.'/api/cities',
            'user_url'      => BASE_URL.'/api/user',
            'token_url'     => BASE_URL.'/api/auth/v1/oauth/token/validate',
            'auth_url'      => BASE_URL.'/api/auth/v1/oauth/token',
            'base_url'      => BASE_URL,
            'client_id'     => 'general-3426',
            'client_secret' => 'client_secret'
        ]
    ]    
);
