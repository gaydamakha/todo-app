<?php

namespace App\Middlewares;

use GuzzleHttp\Exception\ClientException;
use Slim\Http\Response;

class AuthMiddleware
{
    public function __invoke($request, $response, $next)
    {
        try {
            $response = $next($request, $response);
        } catch (ClientException $e) {
            if (401 === $e->getCode()) {
                $response = (new Response())->withRedirect('/signin');
            }
        }

        return $response;
    }
}