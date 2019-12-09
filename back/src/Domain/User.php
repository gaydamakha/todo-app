<?php
declare(strict_types=1);

namespace App\Domain;

use App\Domain\Todo\InvalidAuthorException;
use App\Domain\Todo\InvalidAuthorOrAssigneeException;
use Doctrine\Common\Collections\ArrayCollection;
use JsonSerializable;

/**
 * Class User
 * @package App\Domain\User
 */
class User implements JsonSerializable
{
    /**
     * @var string
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
     * @var ArrayCollection
     */
    private $createdTodos;

    /**
     * @var ArrayCollection
     */
    private $assignedTodos;

    /**
     * @param string $username
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct(string $id, string $username, string $password, string $firstName, string $lastName)
    {
        $this->id = $id;
        $this->username = strtolower($username);
        $this->password = $password;
        $this->firstName = ucfirst($firstName);
        $this->lastName = ucfirst($lastName);
        $this->createdTodos = new ArrayCollection();
        $this->assignedTodos = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId(): string
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
        $this->createdTodos->add($todo);
    }

    /**
     * @return array
     */
    public function getCreatedTodos() : array
    {
        return $this->createdTodos->toArray();
    }

    /**
     * @param
     * @return bool True if todo was in the collection, false otherwise
     */
    public function removeCreatedTodo(Todo $todo): bool
    {
        //Only the author of the Todo have a right to delete it
        return $this->createdTodos->removeElement($todo);
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

        //Remove todo from previous assignee
        $prevAssignee = $todo->getAssignee();

        if (null !== $prevAssignee) {
            $prevAssignee->removeAssignedTodo($todo, $prevAssignee);
        }

        //Add this todo to the new assignee
        $assignee->addAssignedTodo($todo);
        //Mark him as an assignee for this todo
        $todo->setAssignee($assignee);
    }

    /**
     * @param Todo $todo
     */
    public function addAssignedTodo(Todo $todo): void
    {
        $this->assignedTodos->add($todo);
    }

    /**
     * @return array
     */
    public function getAssignedTodos(): array
    {
        return $this->assignedTodos->toArray();
    }

    /**
     * @param Todo $todo
     * @param User $assignee
     * @return bool True if todo was in the collection, false otherwise
     * @throws InvalidAuthorOrAssigneeException
     */
    public function removeAssignedTodo(Todo $todo, User $assignee)
    {
        //Only the author or assignee of the Todo have a right to do it
        if ($todo->getAuthor() !== $this && $todo->getAssignee() !== $this) {
            throw new InvalidAuthorOrAssigneeException();
        }

        $todo->setAssignee(null);
        //TODO: check here because normally right problems
        return $assignee->assignedTodos->removeElement($todo);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'created_todos' => $this->getCreatedTodos(),
            'assigned_todos' => $this->getAssignedTodos()
        ];
    }
}
