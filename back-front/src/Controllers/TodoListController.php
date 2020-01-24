<?php

namespace App\Controllers;

use Adbar\Session;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Slim\Http\Request as ServerRequest;
use Slim\Http\Response;
use Slim\Views\Twig;

class TodoListController
{
    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function addTodoView(ServerRequest $request) {
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

        return $view->render(new Response(),'todospace_add.html.twig', [
            'is_logged' => $session->get('is_logged'),//Normally true
            'todos' => $todos,
            'users' => $users
        ]);
    }

    public function addTodo(ServerRequest $request)
    {
        /** @var Client $client */
        $client = $this->container->get('http_client');
        /** @var Session $session */
        $session = $this->container->get('session');
        $title = $request->getParsedBodyParam('title');
        $description = $request->getParsedBodyParam('description');
        $assignee = $request->getParsedBodyParam('assignee');
        $dueDate = $request->getParsedBodyParam('due_date');
        $data = ['title' => $title];
        if (!empty($description)) {
            $data['description'] = $description;
        }
        if (!empty($assignee)) {
            $data['assignee'] = $assignee;
        }
        if (!empty($dueDate)) {
            $data['due_date'] = $dueDate;
        }
        $body = json_encode(['data' => $data]);

        try {
            $client->request('POST', 'api/todos', [
                'headers' => [
                    'Authorization' => $session->get('token'),
                    'Content-Type' => 'application/json'
                ],
                'body' => $body
            ]);
        } catch (\Exception $e) {
            return (new Response())->withJson($e->getResponse()->getBody()->getContents(), 400);
        }
        //TODO: add flash about success
        return (new Response())->withRedirect("/todos", 301);
    }

    public function assignTodo(ServerRequest $request)
    {
        /** @var Client $client */
        $client = $this->container->get('http_client');
        /** @var Session $session */
        $session = $this->container->get('session');
        $idTask = $request->getAttribute('id');
        $assignee = $request->getParsedBodyParam('assignee');
        $data = ['assignee' => $assignee];
        $body = json_encode(['data' => $data]);

        try {
            $client->request('PATCH', 'api/todos/'. $idTask.'/assign', [
                'headers' => [
                    'Authorization' => $session->get('token'),
                    'Content-Type' => 'application/json'
                ],
                'body' => $body
            ]);
        } catch (\Exception $e) {
            return (new Response())->withJson($e->getResponse()->getBody()->getContents(), 400);
        }
        //TODO: add flash about success
        return (new Response())->withRedirect("/todos/".$idTask, 200);
    }

    public function viewTodo(ServerRequest $request)
    {
        /** @var Twig $view */
        $view = $this->container->get('view');
        /** @var Client $client */
        $client = $this->container->get('http_client');
        /** @var Session $session */
        $session = $this->container->get('session');
        //Fetch the todo from API
        $idTask = $request->getAttribute('id');
        $response = $client->request('GET', 'api/todos/' . $idTask, [
            'headers' => [
                'Authorization' => $session->get('token')
            ]
        ]);
        //TODO: treat error (todo not found)
        $data = json_decode($response->getBody(), true)['data'];
        $todo = $data['todo'];
        $canUpdate = $data['can_update'];

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

        return $view->render(new Response(),'todospace_view.html.twig', [
            'is_logged' => $session->get('is_logged'),//Normally true
            'todos' => $todos,
            'users' => $users,
            'todo' => $todo,
            'can_update' => $canUpdate
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
