<?php
declare(strict_types=1);

namespace App\Application\Actions\Todo;

use App\Application\Actions\PostActionInterface;
use App\Domain\DomainException\DomainInvalidValueException;
use App\Domain\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

//TODO
class UpdateTodoAction extends TodoAction implements PostActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function action(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $body = $request->request->all();
        $this->validateBody($body);
        $todoId = $request->get('id');
        $assigneeUsername = $body['data']['bool'];

//        $this->todoRepository->assignTodo($todoId, $currentUser, $newAssignee);

        $this->logger->info("Todo of id `{$todoId}` was reassigned.");

        return $this->respond($todoId,Response::HTTP_OK);
    }

    /**
     * @param array $body
     * @return void
     */
    public function validateBody(array &$body): void
    {
        $constraints = new Assert\Collection([
            'data' => new Assert\Collection([
                'done' => new Assert\Type('bool')
            ])
        ]);
        $violations = $this->validator->validate($body, $constraints);
        if (0 !== count($violations)) {
            throw new DomainInvalidValueException((string) $violations);
        }
    }
}
