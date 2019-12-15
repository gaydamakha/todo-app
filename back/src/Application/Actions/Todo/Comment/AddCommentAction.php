<?php

namespace App\Application\Actions\Todo\Comment;

use App\Application\Actions\PostActionInterface;
use App\Application\Actions\Todo\TodoAction;
use App\Domain\DomainException\DomainInvalidValueException;
use App\Domain\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

class AddCommentAction extends TodoAction implements PostActionInterface
{
    /**
     * {@inheritDoc}
     */
    public function action(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $body = $request->request->all();
        $this->validateBody($body);
        $todoId = $request->get('id');
        $comment = $body['data']['comment'];

        $this->todoRepository->addComment($todoId, $currentUser, $comment);

        $this->logger->info("A comment for the todo of id `{$todoId}` by user of id `{$currentUser->getId()}` was created.");

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
                'comment' => [
                    new Assert\NotBlank(),
                    new Assert\Type(['type' => 'string'])
                ],
            ])
        ]);

        $violations = $this->validator->validate($body, $constraints);
        if (0 !== count($violations)) {
            throw new DomainInvalidValueException((string) $violations);
        }
    }
}