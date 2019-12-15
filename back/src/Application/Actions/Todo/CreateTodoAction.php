<?php

namespace App\Application\Actions\Todo;

use App\Application\Actions\PostActionInterface;
use App\Domain\DomainException\DomainInvalidValueException;
use App\Domain\Todo;
use App\Domain\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTodoAction extends TodoAction implements PostActionInterface
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
        $data = $body['data'];
        $authorUsername = $currentUser->getUsername();
        $title = $data['title'];
        $description = $data['description'];
        $dueDate = $data['due_date'];
        $assigneeUsername = $data['assignee'];
        $assignee = null;

        $author = $this->userRepository->findUserOfUsername($authorUsername);

        if (null !== $assigneeUsername) {
            $assignee = $this->userRepository->findUserOfUsername($assigneeUsername);
        }

        $todo = $this->todoRepository->createTodo($author, $title, $description, $dueDate, $assignee);

        $todoId = $todo->getId();
        $this->logger->info("Todo of id `{$todoId}` was created.");

        return $this->respond(['id' => $todoId], Response::HTTP_CREATED);
    }

    /**
     * @param array $body
     * @return void
     */
    public function validateBody(array &$body): void
    {
        $constraints = new Assert\Collection([
            'data' => new Assert\Collection([
                'title' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'string'])
                ],
                'due_date' => new Assert\Optional(new Assert\DateTime(['format' => Todo::DATE_TIME_FORMAT])),
                'description' => new Assert\Optional(new Assert\Type(['type' => 'string'])),
                'assignee' => new Assert\Optional(new Assert\Email())
            ])
        ]);

        $violations = $this->validator->validate($body, $constraints);
        if (0 !== count($violations)) {
            throw new DomainInvalidValueException((string) $violations);
        }

        $this->ensureKeyExists($body['data'], 'description', null);
        $this->ensureKeyExists($body['data'], 'due_date', null);
        $this->ensureKeyExists($body['data'], 'assignee',null);
    }
}
