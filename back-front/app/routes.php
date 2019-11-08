<?php

use App\Controllers\TodoListController;
use Slim\App;

return function (App $app) {
    $app->get('/todos', TodoListController::class . ':todos');
};