<?php

use App\Controllers\ApiCallsController;
use App\Controllers\TodoListController;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Http\Response;

return function (App $app) {
    $c = $app->getContainer();
    $app->get('/todos', TodoListController::class . ':todos');

    $app->get('/profile', TodoListController::class . ':profile');

    $app->get('/todos/{id}', ApiCallsController::class . ':viewTodo');

    $app->get('/signin', function() use ($c){
        return  $c->get('view')->render((new Response()),'signin.html.twig');
    });
    $app->post('/signin', ApiCallsController::class.':signin');

    $app->get('/signup', function() use ($c) {
        return $c->get('view')->render(new Response(),'signup.html.twig');
    });
};