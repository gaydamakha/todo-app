<?php

use Adbar\SessionMiddleware;
use Slim\App;

return function (App $app, $settings) {
    $app->add(new SessionMiddleware($settings['session']));
};