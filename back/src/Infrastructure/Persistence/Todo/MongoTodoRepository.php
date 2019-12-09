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
    public function removeTodoOfId(string $id): void
    {
        $todo = $this->findTodoOfId($id);
        $todo->getAuthor()->removeCreatedTodo($todo);
        $todo->getAuthor()->removeAssignedTodo($todo, $todo->getAssignee());
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
    public function assignTodo(string $todoId, User $user, User $assignee): Todo
    {
        $todo = $this->findTodoOfId($todoId);
        $user->assignTodo($todo, $assignee);
        return $todo;
    }
}
