<?php
declare(strict_types=1);

namespace App\Domain;

use App\Domain\Todo\InvalidAuthorException;
use App\Domain\Todo\InvalidAuthorOrAssigneeException;
use App\Domain\Todo\InvalidDueDateException;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use JsonSerializable;

/**
 * Class Todo
 * @package App\Domain\Todo
 */
class Todo implements JsonSerializable
{
    const DATE_TIME_FORMAT = "d/m/yy";

    /**
     * @var string
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
     * @var ArrayCollection
     */
    private $comments;

    /**
     * Todo constructor.
     *
     * @param string $id
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
        string $id,
        User $author,
        string $title,
        string $description=null,
        string $dueDate=null,
        User $assignee=null
    )
    {
        if ($dueDate !== null) {
            $this->dueDate = DateTime::createFromFormat(Todo::DATE_TIME_FORMAT, $dueDate);
            if (new DateTime() > $this->dueDate) {
                throw new InvalidDueDateException();
            }
        }
        $this->id = $id;
        $this->author = $author;
        $this->title = $title;
        $this->description = $description ?? "";
        $this->done = false;
        $this->comments = new ArrayCollection();
        $author->addCreatedTodo($this);
        if (null !== $assignee) {
            $author->assignTodo($this, $assignee);
        }
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
    public function getComments(): array
    {
        return $this->comments->toArray();
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
    public function addComment(string $id, User $commentAuthor, string $comment)
    {
        $comment = new Comment($id, $commentAuthor, $comment);
        $this->comments->add($comment);

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'done' => $this->done,
            //TODO: fix date time format
            'due_date' => null === $this->dueDate? null : $this->dueDate->format('d/m/Y'),
            'author_username' => $this->author->getUsername(),
            'assignee_username' => $this->assignee ? $this->assignee->getUsername() : '',
            'comments' => $this->getComments()
        ];
    }
}
