<?php

function load_config()
{
    $env = '';
    if (isset($_SERVER['DEPLOY_ENV']) && $_SERVER['DEPLOY_ENV']) {
        $env = $_SERVER['DEPLOY_ENV'];
    }
    $filename = __DIR__ . '/configs/main-' . $env . '.php';
    if (!file_exists($filename)) {
        $filename = __DIR__ . '/configs/main.php';
    }
    $config = new Phalcon\Config(require $filename);
    $config->env = $env;
    return $config;
}

function init_loader()
{
    $loader = new Phalcon\Loader;

    $loader->registerDirs([
        __DIR__ . '/tasks/',
        __DIR__ . '/vendors/',
        __DIR__ . '/models/',
        __DIR__ . '/libs/',
    ], true);

    $loader->registerNamespaces([
        'Fruit\Controller' => __DIR__ . '/controllers/',
        'Fruit\Model' => __DIR__ . '/models/',
    ]);

    $loader->register();
}

function init_dependency_injection($config, $isCLI = false)
{
    if ($isCLI) {
        $di = new Phalcon\DI\FactoryDefault\CLI;
    } else {
        $di = new Phalcon\DI\FactoryDefault;
    }

    $di->set('config', $config);

    $di->set('view', function () use ($di) {
        $view = new Phalcon\Mvc\View\Simple;
        $view->setViewsDir(__DIR__ . '/views/');
        $view->registerEngines([
            '.phtml' => 'Phalcon\Mvc\View\Engine\Php',
            '.html' => function ($view, $di) {
                $volt = new Phalcon\Mvc\View\Engine\Volt($view, $di);
                $volt->setOptions([
                    'compiledPath' => __DIR__ . '/cache/',
                    'compiledExtension' => '.compiled',
                ]);
                return $volt;
            }
        ]);
        return $view;
    });

    if ($config->logger) {
        $di->set('logger', function () use ($config) {
            return new Phalcon\Logger\Adapter\File($config->logger->path);
        });
    }

    if (isset($config->db)) {
        $di->set('db', function () use ($config) {
            return new Phalcon\Db\Adapter\Pdo\Mysql($config['db']->toArray());
        });
    }

    // if ($config->cache) {
    //     $di->set('cache', function () use ($config) {
    //         $frontend = new Phalcon\Cache\Frontend\Data($config->cache->frontend->toArray());
    //         return new Phalcon\Cache\Backend\Libmemcached($frontend, $config->cache->backend->toArray());
    //     });
    // }

    // if ($config->queue) {
    //     $di->set('queue', function () use ($config) {
    //         return new Phalcon\Queue\Beanstalk($config->queue->toArray());
    //     });
    // }

    if ($config->mapper) {
        $di->set('mapper', function () use ($config) {
            if (!class_exists('JsonMapper')) {
                include __DIR__ . '/vendors/jsonmapper/JsonMapper.php';
            }
            return new JsonMapper();
        });
    }
    
    if ($config->redis) {
        $di->set('redis', function () use ($config) {
            if (!class_exists('\Predis\Client')) {
                include __DIR__ . '/vendors/predis/autoload.php';
            }
            return new \Predis\Client($config->redis->toArray());
        });
    }

    // if ($config->ip2city) {
    //     $di->set('ip2city', function () use ($config) {
    //         if (!class_exists('\Ip2city')) {
    //             include __DIR__ . '/vendors/geoip/Ip2city.php';
    //         }
    //         return new Ip2city($config->ip2city->db);
    //     });
    // }

    // $di->set('user_id', 0);

    $di->set('util', function () {
        return new Util();
    }, true);
    
    $di->set('curl', function () {
        return new Curl();
    }, true);

    return $di;
}
