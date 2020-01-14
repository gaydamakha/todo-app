<?php

use App\Controllers\TodoListController;
use Slim\App;

return function (App $app) {
    $app->get('/todos', TodoListController::class . ':todos');
    $app->get('/task', TodoListController::class . ':task');
    $app->get('/profile', TodoListController::class . ':profile');
    $app->get('/todos/{id}', TodoListController::class . ':viewTodo');

};