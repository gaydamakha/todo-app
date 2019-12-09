<?php
declare(strict_types=1);

namespace App\Application\Actions\Todo;

use App\Application\Actions\PostActionInterface;
use App\Domain\DomainException\DomainInvalidValueException;
use App\Domain\Todo;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;


class AssignTodoAction extends TodoAction implements PostActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function action(Request $request): Response
    {
        $body = $request->request->all();
        $this->validateBody($body);
        $todoId = $request->get('id');
        $assigneeUsername = $body['data']['username'];
        $assignee = $this->userRepository->findUserOfUsername($assigneeUsername);
        //TODO: fetch user from authorization service
        $user = null;
        $this->todoRepository->assignTodo($todoId, $user, $assignee);

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
                'assignee' => new Assert\Email()
            ])
        ]);
        $violations = $this->validator->validate($body, $constraints);
        if (0 !== count($violations)) {
            throw new DomainInvalidValueException((string) $violations);
        }
    }
}
