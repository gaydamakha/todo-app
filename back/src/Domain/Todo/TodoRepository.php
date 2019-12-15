<?php
declare(strict_types=1);

namespace App\Domain\Todo;

use App\Domain\Todo;
use App\Domain\User;
use DateTime;

interface TodoRepository
{
    /**
     * @return Todo[]
     */
    public function findAll(): array;

    /**
     * @param string $id
     * @return Todo|object
     */
    public function findTodoOfId(string $id);

    /**
     * @param User $author
     * @param string $title
     * @param string|null $description
     * @param DateTime|null $dueDate
     * @param User|null $assignee
     * @return Todo
     */
    public function createTodo(
        User $author,
        string $title,
        string $description = null,
        string $dueDate = null,
        User $assignee = null): Todo;

    /**
     * @param string $id
     * @return void
     */
    public function removeTodoOfId(string $id, User $maybeAuthor): void;

    /**
     * @param string $todoId
     * @param User $user
     * @param User $assignee
     * @return Todo
     */
    public function assignTodo(string $todoId, User $maybeAuthorOrAssignee, ?User $newAssignee): Todo;

    /**
     * @param string $todoId
     * @param User $commentAuthor
     * @param string $comment
     * @return Todo
     */
    public function addComment(string $todoId, User $commentAuthor, string $comment): Todo;
}
