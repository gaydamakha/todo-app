<?php
declare(strict_types=1);

namespace App\Application\Actions\Todo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewTodoAction extends TodoAction
{
    /**
     * {@inheritdoc}
     */
    public function action(Request $request): Response
    {
        $id = $request->get('id');

        $userName = $this->getUser()->getUsername();

        $todo = $this->todoRepository->findTodoOfId($id);

        $this->logger->info("Todo of id `{$id}` was viewed.");

        $authorAndAssigneeNames = [
            $todo->getAuthor()->getUsername(),
            $todo->getAssignee() ? $todo->getAssignee()->getUsername() : ""
        ];

        return $this->respond([
            'todo'=>$todo,
            'can_update' => in_array($userName, $authorAndAssigneeNames)
        ], Response::HTTP_OK);
    }
}
