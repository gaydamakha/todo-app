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

        $todo = $this->todoRepository->findTodoOfId($id);

        $this->logger->info("Todo of id `{$id}` was viewed.");

        return $this->respond($todo, Response::HTTP_OK);
    }
}
