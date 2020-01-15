<?php


namespace App\Controllers;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Container\ContainerInterface;
use Slim\Http\Request as ServerRequest;
use Slim\Http\Response;
use Slim\Views\Twig;

class ApiCallsController
{
    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function viewTodo(ServerRequest $request)
    {
        $idTask = $request->getAttribute('id');
        /** @var Client $client */
        $client = $this->container->get('http_client');
        //Fetch the todos from API
        $todo = $client->request('GET', 'todos/' . $idTask);
        $response = new Response();
        return $response->withJson($todo, 200);
    }

    public function signin(ServerRequest $request)
    {
        $username = $request->getParsedBodyParam('username');
        $password = $request->getParsedBodyParam('password');
        /** @var Client $client */
        $client = $this->container->get('http_client');
//        /** @var Twig $view */
//        $view = $this->container->get('view');

        $request = new Request('POST', '/api/login_check', [],
            json_encode([
                'username' => $username,
                'password' => $password
            ])
        );

        $response = $client->send($request);
        if (200 === $response->getStatusCode()) {
            return (new Response())
                ->withRedirect('/signin', 301)
                ->withHeader('Access-Control-Allow-Origin', 'http://192.168.99.100:8000/')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
                ->withHeader('Authorization', "Bearer ". json_decode($response->getBody(),true)['token']);
        } else {
            //TODO: redirect to signin with flash message
        }

        return new Response();
    }
}