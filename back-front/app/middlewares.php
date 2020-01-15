<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Tuupola\Middleware\JwtAuthentication;

return function (App $app) {
    $app->add(new JwtAuthentication([
//        "environment" => "HTTP_X_TOKEN",//what for?
        "header" => "Authorization",
        "path" => ["/"],
        "ignore" => ["/signin", "/signup"],
        "secure" => false,
        "passthrough" => ["/signin"], //Don't know if use it
        "error" => function (Response $response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            return $response->withStatus(401)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        },
    ]));
};