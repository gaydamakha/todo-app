<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Todo;

use App\Domain\Todo;
use App\Domain\Todo\TodoNotFoundException;
use App\Domain\Todo\TodoRepository;
use App\Domain\User;
use DateTime;
use MongoDB\BSON\ObjectId;

class InMemoryTodoRepository implements TodoRepository
{
    /**
     * @var Todo[]
     */
    private $todos;

    /**
     * InMemoryTodoRepository constructor.
     *
     * @param array|null $todos
     * @throws \App\Domain\Todo\InvalidAuthorException
     * @throws \App\Domain\Todo\InvalidAuthorOrAssigneeException
     * @throws \App\Domain\Todo\InvalidDueDateException
     */
    public function __construct(array $todos = null)
    {
        $fakeUsers = [
            new User((string)(new ObjectId), 'bill.gates@outlook.com', 'password1', 'Bill', 'Gates'),
            new User((string)(new ObjectId), 'steve.jobs@apple.com', 'password2', 'Steve', 'Jobs'),
            new User((string)(new ObjectId), 'mark.zuckerberg@facebook.com', 'password3', 'Mark', 'Zuckerberg'),
            new User((string)(new ObjectId), 'evan.spiegel@snap.com', 'password4', 'Evan', 'Spiegel'),
            new User((string)(new ObjectId), 'jack.dorsey@twitter.com', 'password5', 'Jack', 'Dorsey'),
        ];

        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));

        $fakeTodos = [
            new Todo($fakeUsers[0], 'Todo1', 'Todo assigned to author', $dueDate, $fakeUsers[0]),
            new Todo($fakeUsers[0], 'Todo2', 'Todo assigned to assignee', $dueDate, $fakeUsers[1]),
            new Todo($fakeUsers[1], 'Todo3', 'Todo assigned by assignee of previous todo', $dueDate, $fakeUsers[2]),
            new Todo($fakeUsers[3], 'Todo3', 'Todo assigned with assignee equals to null', $dueDate, null)
        ];

        $this->todos = $todos ?? $fakeTodos;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return $this->todos;
    }

    /**
     * {@inheritdoc}
     */
    public function findTodoOfId(string $id): Todo
    {
        foreach ($this->todos as $todo) {
            if ($id === $todo->getId()) {
                return $todo;
            }
        }

        throw new TodoNotFoundException();
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
        // TODO: Implement addTodo() method.
    }

    /**
     * @param ObjectId $id
     * @return void
     */
    public function removeTodoOfId(string $id, User $maybeAuthor): void
    {
        // TODO: Implement removeTodoOfId() method.
    }

    /**
     * @param string $todoId
     * @param User $user
     * @return Todo
     */
    public function assignTodo(string $todoId, User $maybeAuthorOrAssignee, ?User $newAssignee): Todo
    {
        // TODO: Implement assignTodo() method.
    }

    /**
     * @param string $todoId
     * @param User $commentAuthor
     * @param string $comment
     * @return Todo
     */
    public function addComment(string $todoId, User $commentAuthor, string $comment): Todo
    {
        // TODO: Implement commentTodo() method.
    }
}
