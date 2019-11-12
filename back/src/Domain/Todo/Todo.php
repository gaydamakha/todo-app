<?php
declare(strict_types=1);

namespace App\Domain\Todo;

use App\Domain\User\User;
use DateTime;
use JsonSerializable;
use MongoDB\BSON\ObjectId;

class Todo implements JsonSerializable
{
    /**
     * @var ObjectId
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var boolean
     */
    private $done;

    /**
     * @var DateTime|null
     */
    private $dueDate;

    /**
     * @var User
     */
    private $author;

    /**
     * @var User
     */
    private $assignee;

    /**
     * @var array
     */
    private $comments;

    /**
     * Todo constructor.
     *
     * @param User $author
     * @param string $title
     * @param string $description
     * @param DateTime|null $dueDate
     * @param User|null $assignee
     *
     * @throws InvalidAuthorException
     * @throws InvalidAuthorOrAssigneeException
     * @throws InvalidDueDateException
     */
    public function __construct(
        User $author,
        string $title,
        string $description,
        DateTime $dueDate=null,
        User $assignee=null
    )
    {
        if(new DateTime() > $dueDate) {
            throw new InvalidDueDateException();
        }
        $this->id = new ObjectId();
        $this->author = $author;
        $this->title = $title;
        $this->description = $description;
        $this->dueDate = $dueDate;
        $this->done = false;
        $this->comments = [];
        $author->addCreatedTodo($this);
        if (null !== $assignee) {
            $author->assignTodo($this, $assignee);
        }
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function isDone()
    {
        return $this->done;
    }

    /**
     * @return DateTime|null
     */
    public function getDueDate(): ?DateTime
    {
        return $this->dueDate;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @return User|null
     */
    public function getAssignee(): ?User
    {
        return $this->assignee;
    }

    /**
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }


    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param mixed $done
     */
    public function setDone($done): void
    {
        $this->done = $done;
    }

    /**
     * @param DateTime|null $dueDate
     */
    public function setDueDate(?DateTime $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @param User|null $assignee
     */
    public function setAssignee(?User $assignee): void
    {
        $this->assignee = $assignee;
    }

    /**
     * @param User $commentAuthor
     * @param string $comment
     */
    public function addComment(User $commentAuthor, string $comment)
    {
        $this->comments[] = [
            'comment_author' => $commentAuthor->getUsername(),
            'comment' => $comment
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id->__toString(),
            'title' => $this->title,
            'description' => $this->description,
            'done' => $this->done,
            'due_date' => $this->dueDate,
            'author_username' => $this->author->getUsername(),
            'assignee_username' => $this->assignee ? $this->assignee->getUsername() : '',
            'comments' => $this->comments
        ];
    }
}
