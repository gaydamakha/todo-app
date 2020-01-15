<?php

use Fullpipe\TwigWebpackExtension\WebpackExtension;
use Psr\Container\ContainerInterface;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Router;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use GuzzleHttp\Client;

return function (ContainerInterface $container) {
    $container['view'] = function (ContainerInterface $container) {
        $view = new Twig(__DIR__ . '/../templates', [
            'cache' => false
        ]);

        // Instantiate and add Slim specific extension
        /** @var Router $router */
        $router = $container->get('router');

        $uri = Uri::createFromEnvironment(new Environment($_SERVER));
        $view->addExtension(new TwigExtension($router, $uri));

        $basePath = $router->getBasePath();
        $view->addExtension(new WebpackExtension(
            __DIR__ . '/../public/build/manifest.json',
            $basePath,
            $basePath
        ));

        return $view;
    };
    $container['http_client'] = function(ContainerInterface $container) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://192.168.99.100:8001/',//TODO: get from env
            'timeout'  => 30.0,
            'defaults' => [
                'headers'  => [
                    'Content-type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        ]);

        return $client;
    };
};
