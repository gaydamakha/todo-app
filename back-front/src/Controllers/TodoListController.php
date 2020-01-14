<?php

namespace App\Controllers;

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

    public function todos() {
        /** @var Twig $view */
        $view = $this->container->get('view');
        $client = $this->container->get('http_client');
        //Fetch the todos from API
        $todos = $client->request('GET', 'todos');
        $response = $view->render(new Response(),'todo.html.twig', ['todos' => $todos['data']]);
        return $response;
    }

    public function viewTodo(Request $request){
        $idTask = $request->getAttribute('id');
        $client = $this->container->get('http_client');
        //Fetch the todos from API
        $todo = $client->request('GET', 'todos/' . $idTask);
        $response = new Response();
        return $response->withJson($todo , 200);
    }

    public function task() {
        /** @var Twig $view */
        $view = $this->container->get('view');

        //Fetch the todos from API

        $response = $view->render(new Response(),'task.html.twig', ['name'=>'Micha']);
        return $response;
    }

    public function profile() {
        //TODO: fetch username from session
        $username = "";
        /** @var Twig $view */
        $view = $this->container->get('view');
        $client = $this->container->get('http_client');
        //Fetch the user from API
        $user = $client->request('GET', 'users/' . $username);
        $response = $view->render(new Response(),'profile.html.twig', ['user' => $user]);
        return $response;
    }

    public function signIn() {
        /** @var Twig $view */
        $view = $this->container->get('view');

        //Fetch the todos from API

        $response = $view->render(new Response(),'login.html.twig', ['name'=>'Micha']);
        return $response;
    }

    public function signUp() {
        /** @var Twig $view */
        $view = $this->container->get('view');

        //Fetch the todos from API

        $response = $view->render(new Response(),'register.html.twig', ['name'=>'Micha']);
        return $response;
    }
}
