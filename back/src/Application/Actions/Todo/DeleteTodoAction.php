<?php

namespace App\Application\Actions\Todo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteTodoAction extends TodoAction
{
    /**
     * {@inheritdoc}
     */
    public function action(Request $request): Response
    {
        $todoId = $request->get('id');

        $this->todoRepository->removeTodoOfId($todoId);
        //TODO: verify if the user have a right to remove the TODO
        $this->logger->info("Todo of id `{$todoId}` was deleted.");
        //change code
        return $this->respond($todoId, Response::HTTP_OK);
    }
}