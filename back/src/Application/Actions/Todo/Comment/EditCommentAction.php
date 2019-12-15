<?php


namespace App\Application\Actions\Todo\Comment;


use App\Application\Actions\PostActionInterface;
use App\Application\Actions\Todo\TodoAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditCommentAction extends TodoAction implements PostActionInterface
{

    /**
     * {@inheritDoc}
     */
    public function action(Request $request): Response
    {
        $todoId = new ObjectId($this->resolveArg('id'));
        $todo = $this->todoRepository->findTodoOfId($todoId);
        $body = $this->request->getParsedBody();
        $this->validateBody($body);
//        $commentText = $body['data']['comment'];
        //$commentAuthor = $this->auth->getCurrentUser(); //TODO: fetch current user after get verification done
//        $todo->addComment($commentAuthor, $commentText);

        $this->logger->info("A comment for the todo of id `{$todoId}` was created.");//TODO: add Username here

        //change code
        return $this->respond($todoId);
    }

    /**
     * @param array $body
     * @return void
     */
    public function validateBody(array &$body): void
    {
        //TODO: rewrite validation
        $v = $this->validator;
        if (!$v::key('data')->validate($body)) {
            throw new InvalidRequestPayloadException($this->request, "Key 'data' not found in request payload");
        }
        $data = $body['data'];
        try {
            $v->keySet(
                new Key('comment', $v::allOf(
                    $v::stringType(),
                    $v::notBlank(),
                    $v::notEmpty()
                ))
            )->assert($data);
        } catch (\Exception $e) {
            throw new InvalidRequestPayloadException($this->request, $e->getFullMessage());
        }
    }
}