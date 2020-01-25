<?php

use Adbar\Session;
use App\Controllers\SigninController;
use App\Controllers\TodoListController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Http\Response;
use Slim\Http\Request as ServerRequest;

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

        $app->delete('/todos/{id}', TodoListController::class . ':deleteTodo');
    })->add(new AuthMiddleware());


    $app->get('/signin', function () use ($c) {
        /** @var Session $session */
        $session = $c->get('session');
        $flashError = $session->get('flash_error')['0'];
        $flashSuccess = $session->get('flash_success')[0];
        $session->delete(['flash_error', 'flash_success']);
        if ($session->get('is_logged')) {
            return (new Response())->withRedirect('/todos');
        }

        return $c->get('view')->render(new Response(), 'signin.html.twig', [
            'flash_error' => $flashError,
            'flash_success' => $flashSuccess
        ]);
    });
    $app->post('/signin', SigninController::class . ':signin');

    $app->get('/logout', function () use ($c) {
        /** @var Session $session */
        $session = $c->get('session');
        $session->delete('token');
        $session->delete('username');
        $session->set('is_logged', false);
        return (new Response())->withRedirect('/signin', 301);
    });

    $app->get('/signup', function (ServerRequest $request) use ($c) {
        /** @var Session $session */
        $session = $c->get('session');

        $flashError = $session->get('flash_error')[0];
        $flashSuccess = $session->get('flash_success')[0];

        $session->delete(['flash_error', 'flash_success']);
        return $c->get('view')->render(new Response(), 'signup.html.twig', [
            'flash_error' => $flashError,
            'flash_success' => $flashSuccess
        ]);
    });

    $app->post('/signup', SigninController::class . ':signup');
};