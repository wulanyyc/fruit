<?php

return [
    // 'cache' => [
    //     'frontend' => [
    //         'lifetime' => 86400,
    //     ],
    //     'backend' => [
    //         'servers' => [
    //             ['host' => '127.0.0.1', 'port' => 11211, 'weight' => 1],
    //         ],
    //     ],
    // ],

    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'username' => 'root',
        'password' => 'testmysql123',
        'dbname' => 'fruits',
        'options' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ],
    ],
    // 'queue' => [
    //     'host' => '127.0.0.1',
    //     'port' => 11300,
    // ],
    'logger' => ['path' => __DIR__ . '/../logs/app.log'],
    'redis' => [
        'tcp://127.0.0.1',
    ],
    'mapper' => [],
    'ip2city' => [
        'db' => '/opt/geo_ip/GeoIP2-City.mmdb'
    ],
    'hotel_api' => [
        "url" => "http://api.admin.qa.toursforfun.com/",
        "timeout" => 30,
    ],
    'salt' => 'yyctest',
    'deploy' => 'testing'
];
