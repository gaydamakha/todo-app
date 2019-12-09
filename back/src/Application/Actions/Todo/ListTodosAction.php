<?php

namespace App\Application\Actions\Todo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListTodosAction extends TodoAction
{
    /**
     * {@inheritdoc}
     */
    public function action(Request $request): Response
    {
        $todos = $this->todoRepository->findAll();

        $this->logger->info("Todos list was viewed.");
        //change code
        return $this->respond($todos, Response::HTTP_OK);
    }
}
