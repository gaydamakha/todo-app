<?php

namespace App\Infrastructure\Persistence\Todo;

use App\Domain\Todo;
use App\Domain\Todo\TodoNotFoundException;
use App\Domain\Todo\TodoRepository;
use App\Domain\User;
use App\Infrastructure\Persistence\MongoRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;

class MongoTodoRepository extends MongoRepository implements TodoRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, Todo::class);
    }

    /**
     * @param string $id
     * @return Todo|object
     * @throws TodoNotFoundException
     */
    public function findTodoOfId(string $id)
    {
        $todo = $this->find($id);
        if (null === $todo) {
            throw new TodoNotFoundException();
        }

        return $todo;
    }

    /**
     * @param Todo $todo
     * @return void
     */
    public function createTodo(
        User $author,
        string $title,
        string $description = null,
        string $dueDate = null,
        User $assignee = null): Todo
    {
        $todo = new Todo((string)(New ObjectId()), $author, $title, $description, $dueDate, $assignee);
        $this->dm->persist($todo);
        $this->dm->flush();
        return $todo;
    }

    /**
     * @param string $id
     * @return void
     * @throws TodoNotFoundException
     * @throws Todo\InvalidAuthorOrAssigneeException
     */
    public function removeTodoOfId(string $id, User $maybeAuthor): void
    {
        $todo = $this->findTodoOfId($id);
        //Throws an exception if it is not an author
        $maybeAuthor->removeCreatedTodo($todo);
        $this->dm->remove($todo);
        $this->dm->flush();
    }

    /**
     * @param string $todoId
     * @param User $user
     * @return Todo
     * @throws TodoNotFoundException
     * @throws Todo\InvalidAuthorOrAssigneeException
     */
    public function assignTodo(string $todoId, User $maybeAuthorOrAssignee, ?User $newAssignee): Todo
    {
        $todo = $this->findTodoOfId($todoId);
        //Throws an exception if it's not a user or assignee
        $maybeAuthorOrAssignee->assignTodo($todo, $newAssignee);
        $this->dm->flush();
        return $todo;
    }

    /**
     * @param string $todoId
     * @param User $commentAuthor
     * @param string $comment
     * @return Todo
     */
    public function addComment(string $todoId, User $commentAuthor, string $comment): Todo
    {
        $todo = $this->findTodoOfId($todoId);
        $todo->addComment((string)(New ObjectId()), $commentAuthor, $comment);
        $this->dm->flush();
        return $todo;
    }
}
