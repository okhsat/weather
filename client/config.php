<?php
use Phalcon\Config;

return new Config(
    [
        'application' => [
            'viewsDir'       => __DIR__ . '/views',
            'controllersDir' => __DIR__
        ],
        
        'api' => [
            'prf_upload_url' => BASE_URL.'/api/prf-upload',
            'coupon_url'     => BASE_URL.'/api/coupon',
            'cities_url'     => BASE_URL.'/api/cities',
            'user_url'       => BASE_URL.'/api/user',
            'token_url'      => BASE_URL.'/api/auth/v1/oauth/token/validate',
            'auth_url'       => BASE_URL.'/api/auth/v1/oauth/token',
            'base_url'       => BASE_URL,
            'client_id'      => 'general-3426',
            'client_secret'  => 'iytds43wert67y8urrdfg6766dfg676drf7'
        ]
    ]    
);
