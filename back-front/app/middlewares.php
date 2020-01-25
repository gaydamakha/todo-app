<?php

use Adbar\SessionMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app, $settings) {
    $app->add(new SessionMiddleware($settings['session']));
//
//    $app->add(function (Request $request, Response $response, callable $next) use ($app) {
//        /** @var \Adbar\Session $c */
//        $session = $app->getContainer()->get('session');
//        $session->delete(['error', 'flash_error', 'flash_success']);
//
//        return $next($request, $response);
    };