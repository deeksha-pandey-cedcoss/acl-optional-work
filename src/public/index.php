<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Config\ConfigFactory;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Http\Response\Cookies;
use time\Time;
use listen\Listner;


$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
        APP_PATH . "/etc/",
    ]
);

$loader->registerNamespaces(
    [
        'time' => APP_PATH . "/assets/",
        'MyApp\handler' => APP_PATH . '/handlers/',
        'listen' => APP_PATH . "/handlers/"
    ]
);
$loader->registerclasses(
    [
        'listner' => APP_PATH . "/listner/listner.php",
        'Listner'   => APP_PATH . '/handlers/Listner.php',
    ]
);
$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$container->set(
    'db',
    function () {
        return new Mysql($this['config']->db->toArray());
    }
);
$container->set(
    'config',
    function () {
        $fileName = '../app/etc/config.php';
        $factory = new ConfigFactory();
        return $config = $factory->newInstance('php', $fileName);
    }
);
$container->setShared(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session->setAdapter($files)->start();
        return $session;
    }

);
$container->set('cookies', function () {
    $cookies = new Cookies();

    $cookies->useEncryption(false);

    return $cookies;
});
$container->set(
    'time',
    function () {
        return new Time();
    }
);

$container->set(
    'mongo',
    function () {
        $mongo = new MongoClient();

        return $mongo->selectDB('phalt');
    },
    true
);

$eventsManager = $container->get('eventsManager');
$eventsManager->attach(
    'application:beforeHandleRequest',
    new Listner()
);
$container->set(
    'EventsManager',
    $eventsManager
);

$application = new Application($container);

$application->setEventsManager($eventsManager);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
