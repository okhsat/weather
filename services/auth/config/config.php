<?php
use Phalcon\Config;

define('SCOPE_DELIMITER_STRING', ' ');

try {
    $oneMonthInterval   = new \DateInterval('P1M');
    $oneHourInterval    = new \DateInterval('PT1H');
    $tenMinutesInterval = new \DateInterval('PT10M');
    
} catch (Exception $e) {
}

$config = [
    'appParams' => [
        'appNamespace' => 'App',
        'appName'      => 'Padlock! The Phalcon Authentication Server',
        'appVersion'   => '0.1'
    ],
    
    'application' => [
        'modelsDir'       => __DIR__ . '/../models/',
        'controllersDir'  => __DIR__ . '/../controllers/',
        'libsDir'         => __DIR__ . '/../library/',
        'interfacesDir'   => __DIR__ . '/../interfaces/',
        'validationsDir'  => __DIR__ . '/../validations/',
        'logsDir'         => __DIR__ . '/../logs/',
        'repositoriesDir' => __DIR__ . '/../repositories/',
    ],

    'database' => [
        'adapter'  => 'Mysql',
        'host'     => '141.136.41.1',
        'dbname'   => 'dbname',
        'username' => 'username',
        'password' => 'password',
        'port'     => '3306'
    ],

    'oauth' => [
        'refresh_token_lifespan'       => $oneMonthInterval,
        'access_token_lifespan'        => $oneHourInterval,
        'auth_code_lifespan'           => $tenMinutesInterval,
        'always_include_client_scopes' => true,
        'encryption_key'               => 'jFlJKM46QALyHUZFyZrSGaEsveb8sfCm7av9DIVmh4qGg17Ha5RMuA==',
        'public_key_path'              => __DIR__ . '/../keys/public.key',
        'private_key_path'             => __DIR__ . '/../keys/private.key'
    ],

    'debug' => getenv('DEBUG') === 'true',
];

return new Config($config);
