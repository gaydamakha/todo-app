<?php
declare(strict_types=1);

namespace App\Application\Actions\Todo;

use MongoDB\BSON\ObjectId;
use Psr\Http\Message\ResponseInterface as Response;

class ViewTodoAction extends TodoAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $id = new ObjectId($this->resolveArg('id'));
        $todo = $this->todoRepository->findTodoOfId($id);

        $this->logger->info("Todo of id `{$id}` was viewed.");

        return $this->respondWithData($todo);
    }
}
