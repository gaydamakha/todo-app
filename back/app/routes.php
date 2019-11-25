<?php
declare(strict_types=1);

use App\Application\Actions\Todo\ListTodosAction;
use App\Application\Actions\Todo\ViewTodoAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{username}', ViewUserAction::class);
    });

    $app->group('/todos', function (Group $group) {
        $group->get('', ListTodosAction::class);
        $group->get('/{id}', ViewTodoAction::class);
    });
};