<?php


namespace App\Domain;


use JsonSerializable;
use MongoDB\BSON\ObjectId;

class Comment implements JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var User
     */
    private $author;

    /**
     * @var string
     */
    private $text;

    public function __construct(string $id, User $author, string $text)
    {
        $this->id = $id;
        $this->author = $author;
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return mixed|void
     */
    public function jsonSerialize()
    {
        return [
            'author' => $this->author->getUsername(),
            'text' => $this->text
        ];
    }
}