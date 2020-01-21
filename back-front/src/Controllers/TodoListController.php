<?php

namespace App\Controllers;

use Adbar\Session;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class TodoListController
{
    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function todos(Request $request) {
        /** @var Twig $view */
        $view = $this->container->get('view');
        /** @var Client $client */
        $client = $this->container->get('http_client');
        /** @var Session $session */
        $session = $this->container->get('session');
        //Fetch the todos from API
        //TODO: treat possible errors
        $response = $client->request('GET', 'api/todos', [
            'headers'  => [
                'Authorization'=> $session->get('token')
            ]
        ]);

        $todos = json_decode($response->getBody(), true)['data'];

        $response = $client->request('GET', 'api/users', [
            'headers'  => [
                'Authorization'=> $session->get('token')
            ]
        ]);

        $users = json_decode($response->getBody(), true)['data'];

        return $view->render(new Response(),'todospace.html.twig', [
            'is_logged' => $session->get('is_logged'),//Normally true
            'todos' => $todos,
            'users' => $users
        ]);
    }

    public function profile() {
        /** @var Twig $view */
        $view = $this->container->get('view');
        /** @var Client $client */
        $client = $this->container->get('http_client');
        /** @var Session $session */
        $session = $this->container->get('session');

        $username = $session->get('username');
        //Fetch the user from API
        $response = $client->request('GET', 'api/users/' . $username, ['headers'  => [
            'Authorization'=> $session->get('token')
        ]]);
        $user = json_decode($response->getBody(), true)['data'];

        return $view->render(new Response(),'profile.html.twig', [
            'is_logged' => $session->get('is_logged'),//Normally true
            'user' => $user
        ]);
    }
}
