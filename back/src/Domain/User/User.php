<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Todo\AssignedTodoNotFoundException;
use App\Domain\Todo\CreatedTodoNotFoundException;
use App\Domain\Todo\InvalidAuthorException;
use App\Domain\Todo\InvalidAuthorOrAssigneeException;
use App\Domain\Todo\Todo;
use JsonSerializable;
use MongoDB\BSON\ObjectId;

class User implements JsonSerializable
{
    /**
     * @var ObjectId
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var array
     */
    private $createdTodos;

    /**
     * @var array
     */
    private $assignedTodos;

    /**
     * @param string $username
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     * @throws InvalidEmailException
     */
    public function __construct(string $username, string $password, string $firstName, string $lastName)
    {
        $this->id = new ObjectId();
        $this->username = $this->validateEmail($username);
        $this->password = $password;
        $this->firstName = ucfirst($firstName);
        $this->lastName = ucfirst($lastName);
        $this->createdTodos = [];
        $this->assignedTodos = [];
    }

    /**
     * @return ObjectId
     */
    public function getId(): ObjectId
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }


    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param Todo $todo
     * @throws InvalidAuthorException
     */
    public function addCreatedTodo(Todo $todo)
    {
        //It is possible to add only a todo created by $this
        if ($todo->getAuthor() !== $this) {
            throw new InvalidAuthorException();
        }
        $this->createdTodos[$todo->getId()->__toString()] = $todo;
    }

    /**
     * @return array
     */
    public function getCreatedTodos() : array
    {
        return array_values($this->createdTodos);
    }

    /**
     * @param ObjectId $todoId
     *
     * @throws CreatedTodoNotFoundException
     */
    public function removeCreatedTodo(ObjectId $todoId)
    {
        //Only the author of the Todo have a right to delete it
        if (!isset($this->createdTodos[$todoId->__toString()])) {
            throw new CreatedTodoNotFoundException();
        }

        unset($this->createdTodos[$todoId->__toString()]);
    }

    /**
     * @param Todo $todo
     * @param User $assignee
     * @throws InvalidAuthorOrAssigneeException
     */
    public function assignTodo(Todo $todo, User $assignee)
    {
        //Only Author or Assignee have a right to reassign a todo
        if ($todo->getAuthor() !== $this && $todo->getAssignee() !== $this) {
            throw new InvalidAuthorOrAssigneeException();
        }

        $todoId = $todo->getId();
        //Remove todo from previous assignee
        $prevAssignee = $todo->getAssignee();

        if (null !== $prevAssignee) {
            $prevAssignee->removeAssignedTodo($todoId, $prevAssignee);
        }

        //Add this todo to the new assignee
        $assignee->assignedTodos[$todoId->__toString()] = $todo;
        //Mark him as an assignee for this todo
        $todo->setAssignee($assignee);
    }

    /**
     * @return array
     */
    public function getAssignedTodos(): array
    {
        return array_values($this->assignedTodos);
    }

    public function removeAssignedTodo(ObjectId $todoId, User $assignee)
    {
        if (!isset($assignee->assignedTodos[$todoId->__toString()])) {
            throw new AssignedTodoNotFoundException();
        }
        $todo = $assignee->assignedTodos[$todoId->__toString()];
        //Only the author or assignee of the Todo have a right to do it
        if ($todo->getAuthor() !== $this && $todo->getAssignee() !== $this) {
            throw new InvalidAuthorOrAssigneeException();
        }

        $todo->setAssignee(null);
        unset($assignee->assignedTodos[$todoId->__toString()]);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id->__toString(),
            'username' => $this->username,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'created_todos' => $this->getCreatedTodos(),
            'assigned_todos' => $this->getAssignedTodos()
        ];
    }

    /**
     * @param $username
     *
     * @return string
     * @throws InvalidEmailException
     */
    private function validateEmail($username)
    {
        $username = filter_var(strtolower($username), FILTER_SANITIZE_EMAIL);
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException();
        }

        return $username;
    }
}
