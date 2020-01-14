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

        //Fetch the todos from API

        $response = $view->render(new Response(),'todo.html.twig', ['name'=>'Micha']);
        return $response;
    }

    public function viewTodo(Request $request){
        $idTask = $request->getAttribute("id");
        $response = new Response();
        return $response->withJson(['id' => $idTask ], 200);
    }

    public function task() {
        /** @var Twig $view */
        $view = $this->container->get('view');

        //Fetch the todos from API

        $response = $view->render(new Response(),'task.html.twig', ['name'=>'Micha']);
        return $response;
    }

    public function profile() {
        /** @var Twig $view */
        $view = $this->container->get('view');

        //Fetch the todos from API

        $response = $view->render(new Response(),'profile.html.twig', ['name'=>'Micha']);
        return $response;
    }
}
