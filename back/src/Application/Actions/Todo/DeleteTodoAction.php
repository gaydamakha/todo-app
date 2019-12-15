<?php

namespace App\Application\Actions\Todo;

use App\Domain\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteTodoAction extends TodoAction
{
    /**
     * {@inheritdoc}
     */
    public function action(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $todoId = $request->get('id');
        $this->todoRepository->removeTodoOfId($todoId, $currentUser);

        $this->logger->info("Todo of id `{$todoId}` was deleted.");

        return $this->respond($todoId, Response::HTTP_OK);
    }
}