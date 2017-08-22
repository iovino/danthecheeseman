<?php

// set important ini values
ini_set('display_errors', true);
ini_set('log_errors', true);

// track start time
define('START_TIME', microtime(true));
define('APP_VERSION', '1.0.0');

// define app paths
define('APP_PATH'   , __DIR__  . '/..');
define('SRC_PATH'   , APP_PATH . '/src');
define('VIEWS_PATH' , APP_PATH . '/views');
define('CONFIG_PATH', APP_PATH . '/config');

// load dependencies
require APP_PATH    . '/vendor/autoload.php';
require CONFIG_PATH . '/config.php';

// initiate application
$app = new \Slim\App(["settings" => $config]);

// load routes
require CONFIG_PATH . '/routes.php';

// build any containers
$container = $app->getContainer();
$container['view'] = new \Slim\Views\PhpRenderer(VIEWS_PATH);

// start
$app->run();