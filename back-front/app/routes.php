<?php

use Adbar\Session;
use App\Controllers\SigninController;
use App\Controllers\TodoListController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Http\Response;

return function (App $app) {
    $c = $app->getContainer();
    $app->group('', function (App $app) {
        $app->get('/', function () {
            return (new Response())->withRedirect('/signin');
        });
        $app->get('/todos', TodoListController::class . ':addTodoView');

        $app->post('/todos', TodoListController::class . ':addTodo');

        $app->get('/profile', TodoListController::class . ':profile');

        $app->get('/todos/{id}', TodoListController::class . ':viewTodo');

        $app->post('/todos/{id}/assign', TodoListController::class . ':assignTodo');
    })->add(new AuthMiddleware());


    $app->get('/signin', function() use ($c) {
        /** @var Session $session */
        $session = $c->get('session');
        if ($session->get('is_logged')) {
            return (new Response())->withRedirect('/todos');
        }
        return  $c->get('view')->render(new Response(), 'signin.html.twig');
    });
    $app->post('/signin', SigninController::class . ':signin');

    $app->get('/logout', function() use ($c) {
        /** @var Session $session */
        $session = $c->get('session');
        $session->delete('token');
        $session->delete('username');
        $session->set('is_logged', false);
        return (new Response())->withRedirect('/signin', 301);
    });
    $app->get('/signup', function() use ($c) {
        /** @var Session $session */
        return $c->get('view')->render(new Response(), 'signup.html.twig');
    });
};