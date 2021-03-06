<?php

use Slim\App;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../app/settings.php';

$app = new App($config);

$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

$middlewares = require __DIR__ . '/../app/middlewares.php';
$middlewares($app, $config['settings']);

$container = $app->getContainer();

$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($container);

$app->run();