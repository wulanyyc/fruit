<?php

return [
    'host_url' => 'https://app.toursforfun.com',
    'h5mobile_url' => 'http://m.toursforfun.com',
    'h5notify_url' => 'http://cn.toursforfun.com/Cart/payNotify',
    'payment_url' => 'https://payment.toursforfun.com',
    'services' => [
        'Provider' => [
            'url' =>'@@SERVICES_PROVIDER_URL@@',
            'secret' => '@@SERVICES_PROVIDER_SECRET@@',
        ],
        'Product' => [
            'url' =>'@@SERVICES_PRODUCT_URL@@',
            'secret' => '@@SERVICES_PRODUCT_SECRET@@',
        ],
        'Affiliate' => [
            'url' =>'@@SERVICES_AFFILIATE_URL@@',
            'secret' => '@@SERVICES_AFFILIATE_SECRET@@',
        ],
        'Content' => [
            'url' =>'@@SERVICES_CONTENT_URL@@',
            'secret' => '@@SERVICES_CONTENT_SECRET@@',
        ],
        'Order' => [
            'url' =>'@@SERVICES_ORDER_URL@@',
            'secret' => '@@SERVICES_ORDER_SECRET@@',
        ],
        'Resource' => [
            'url' =>'@@SERVICES_RESOURCE_URL@@',
            'secret' => '@@SERVICES_RESOURCE_SECRET@@',
        ],
        'User' => [
            'url' =>'@@SERVICES_USER_URL@@',
            'secret' => '@@SERVICES_USER_SECRET@@',
        ],
    ],

    'payment_service' => [
        'url' => 'http://payment.services.tff.com',
        'app_id' => 'h5mobile_site'
    ],
    'cache' => [
        'frontend' => [
            'lifetime' => @@RESTFUL_CACHE_LIFETIME@@,
        ],
        'backend' => [
            'servers' => [
                ['host' => '@@RESTFUL_CACHE_HOST@@', 'port' => @@RESTFUL_CACHE_PORT@@, 'weight' => @@RESTFUL_CACHE_WEIGHT@@],
            ],
        ],
    ],

    'db' => [
        'host' => '@@APP_DB_SERVER_HOST@@',
        'port' => '@@APP_DB_SERVER_PORT@@',
        'username' => '@@APP_DB_SERVER_USERNAME@@',
        'password' => '@@APP_DB_SERVER_PASSWORD@@',
        'dbname' => '@@APP_DB_NAME@@',
        'options' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ],
    ],

    'queue' => [
        'host' => '@@RESTFUL_QUEUE_HOST@@',
        'port' => @@RESTFUL_QUEUE_PORT@@,
    ],
    'logger' => ['path' => '@@RESTFUL_PRODUCT_LOGGER_PATH@@'],
    'picture' => ['path' => '@@RESTFUL_PRODUCT_PICTURE_PATH@@'],
    'redis' => ['@@RESTFUL_REDIS_HOST@@'],
    'mapper' => [],
    'ip2city' => [
        'db' => '@@RESTFUL_GEOIP_FILE_PATH@@'
    ],
    'phonebooking' => 7,
    'hotel_api' => [
        //"url" => "http://hotel.services.tff.com/",
        "url" => "http://api.admin.tff.so/",
        "timeout" => 30, 
    ],

];
