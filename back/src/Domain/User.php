<?php
declare(strict_types=1);

namespace App\Domain;

use App\Domain\Todo\InvalidAuthorException;
use App\Domain\Todo\InvalidAuthorOrAssigneeException;
use Doctrine\Common\Collections\ArrayCollection;
use JsonSerializable;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package App\Domain\User
 */
class User implements JsonSerializable, UserInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * It's an email, in fact
     *
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
        $author = $todo->getAuthor();
        //Only the assignee of the Todo have a right to do it
        if ($author !== $this) {
            throw new InvalidAuthorException();
        }
        //desassign this todo from the assignee
        $todo->getAssignee()->removeAssignedTodo($todo);

        return $this->createdTodos->removeElement($todo);
    }

    /**
     * @param Todo $todo
     * @param User|null $newAssignee
     * @throws InvalidAuthorOrAssigneeException
     */
    public function assignTodo(Todo $todo, ?User $newAssignee)
    {
        //Only Author or Assignee have a right to assign a todo
        if ($todo->getAuthor() !== $this && $todo->getAssignee() !== $this) {
            throw new InvalidAuthorOrAssigneeException();
        }

        //Remove todo from previous assignee
        $prevAssignee = $todo->getAssignee();

        if (null !== $prevAssignee) {
            $prevAssignee->removeAssignedTodo($todo);
        }

        //Assign new person
        $todo->setAssignee($newAssignee);

        //Add this todo to the new assignee
        if (null !== $newAssignee) {
            $newAssignee->addAssignedTodo($todo);
        }
    }

    /**
     * @param Todo $todo
     */
    private function addAssignedTodo(Todo $todo): void
    {
        //As it is private, no problem (only a developer of the Domain can call it)
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
    public function removeAssignedTodo(Todo $todo)
    {
        $assignee = $todo->getAssignee();
        //Only the assignee of the Todo have a right to do it
        if ($assignee !== $this) {
            throw new InvalidAuthorOrAssigneeException();
        }
        $todo->setAssignee(null);

        return $this->assignedTodos->removeElement($todo);
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

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }

}
