<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
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



        $response = $view->render(new Response(),'todolist.html.twig', ['name'=>'Micha']);
        return $response;
    }
}
