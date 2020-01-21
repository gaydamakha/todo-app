<?php


namespace App\Controllers;


use Adbar\Session;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Container\ContainerInterface;
use Slim\Http\Request as ServerRequest;
use Slim\Http\Response;

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
        /** @var Session $session */
        $session = $this->container->get('session');
        //Fetch the todo from API
        $response = $client->request('GET', 'api/todos/' . $idTask, [
            'headers'  => [
                'Authorization' => $session->get('token')
            ]
        ]);
        //TODO: treat error (todo not found)
        $todo = json_decode($response->getBody(),true)['data'];

        return (new Response())->withJson($todo, 200);
    }

    public function signin(ServerRequest $request)
    {
        $username = $request->getParsedBodyParam('username');
        $password = $request->getParsedBodyParam('password');
        /** @var Client $client */
        $client = $this->container->get('http_client');
        /** @var Session $session */
        $session = $this->container->get('session');

        $request = new Request('POST', '/api/login_check', [],
            json_encode([
                'username' => $username,
                'password' => $password
            ])
        );

        $response = $client->send($request);

        if (200 === $response->getStatusCode()) {
            $session->set('username', $username);
            $session->set('token', 'Bearer '. json_decode($response->getBody(), true)['token']);
            $session->set('is_logged', true);
            return (new Response())
                ->withRedirect('/todos', 301);
        }

        //TODO: redirect to signin with flash error message (text from json response)
        return new Response();
    }
}