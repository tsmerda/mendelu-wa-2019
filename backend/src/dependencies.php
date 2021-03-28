<?php
// DIC configuration

$container = $app->getContainer();

use Latte\Engine;
use Ujpef\LatteView;

$container['view'] = function ($container) use ($settings) {
    $engine = new Engine();
    $engine->setTempDirectory(__DIR__ . '/../cache');

    $latteView = new LatteView($engine, __DIR__ . '/../templates/');
    return $latteView;
};

$container['db'] = function ($container) use ($settings) {
    $s = $settings['settings']['db'];
    $pdo = new PDO('pgsql:host=' . $s['host'] .
                   ';dbname=' . $s['name'],
            $s['user'],    //login
            $s['pass']  //heslo k db, pwlogin
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE,
                       PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,
                       PDO::FETCH_ASSOC);
    return $pdo;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
