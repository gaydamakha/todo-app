<?php


namespace Domain\Todo;

use DateTime;
use Tests\TestCase;
use App\Domain\Todo\Todo;
use App\Domain\User\User;

class TodoTest extends TestCase
{
    public function validTodoProvider()
    {
        $author = new User('author@mail.ru','password', 'author', 'notmatter');
        $assignee = new User('assignee@gmail.com', 'password2', 'Assignee', 'Worker');
        $dueDate = new DateTime();
        $dueDate->modify("+1 month");

        return [
            [$author, 'Todo1', 'Good todo', $dueDate, $assignee],
            [$author, 'Todo2', 'Good todo', $dueDate, $author],
            [$author, 'Todo3', 'Good todo', $dueDate, null]
        ];
    }

    public function notValidTodoProvider()
    {
        $author = new User('author@mail.ru','password', 'author', 'notmatter');
        $assignee = new User('assignee@gmail.com', 'password2', 'Assignee', 'Worker');
        $dueDate = new DateTime();
        $dueDate->modify("-1 day");

        return [
            [$author, 'Todo1', 'Good todo', $dueDate, $assignee]
        ];
    }

    public function addCommentDataProvider()
    {
        $commentAuthor = new User(
            'comment_author@mail.ru',
            'password',
            'commentauthor',
            'notmatter'
        );

        $assignee= new User(
            'assignee@mail.ru',
            'password',
            'assignee',
            'doallwork'
        );

        $thirdPerson = new User(
            'third@mail.ru',
            'password',
            'nobody',
            'justseeandgoaway'
        );

        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todoByCommentAuthor = new Todo($commentAuthor, 'todo1', 'first todo', $dueDate, null);
        $todoWithAssignee = new Todo($commentAuthor, 'todo1', 'first todo', $dueDate, $assignee);

        return [
            [$todoByCommentAuthor, $commentAuthor, 'I wrote a comment for my todo'],
            [$todoByCommentAuthor, $thirdPerson, 'I wrote a comment for something that I see first time'],
            [$todoWithAssignee, $assignee, 'I wrote a comment because I don\'t understand what should I do'],
            [$todoWithAssignee, $commentAuthor, 'I wrote a comment to my assignee']
        ];
    }

    /**
     * @dataProvider validTodoProvider
     *
     * @param $author
     * @param $title
     * @param $description
     * @param $dueDate
     * @param $assignee
     */
    public function testGetters($author, $title, $description, $dueDate, $assignee)
    {
        $todo = new Todo($author, $title, $description, $dueDate, $assignee);

        $this->assertEquals($author, $todo->getAuthor());
        $this->assertEquals($title, $todo->getTitle());
        $this->assertEquals($description, $todo->getDescription());
        $this->assertEquals($dueDate, $todo->getDueDate());
        $this->assertEquals($assignee, $todo->getAssignee());
    }

    /**
     * @dataProvider notValidTodoProvider
     *
     * @expectedException \App\Domain\Todo\InvalidDueDateException
     *
     * @param $author
     * @param $title
     * @param $description
     * @param $dueDate
     * @param $assignee
     */
    public function testInvalidDateTodo($author, $title, $description, $dueDate, $assignee)
    {
        new Todo($author, $title, $description, $dueDate, $assignee);
    }

    /**
     * @dataProvider addCommentDataProvider
     *
     * @param $todo
     * @param $commentAuthor
     * @param $comment
     */
    public function testAddComment($todo, $commentAuthor, $comment)
    {
        $todo->addComment($commentAuthor, $comment);

        $expectedComment = [
            'comment_author' => $commentAuthor->getUsername(),
            'comment' => $comment
        ];

        $this->assertContains($expectedComment, $todo->getComments());
    }

    /**
     * Checks for valid Json serialization
     *
     * @dataProvider validTodoProvider
     *
     * @param $author
     * @param $title
     * @param $description
     * @param $dueDate
     * @param $assignee
     */
    public function testJsonSerialize($author, $title, $description, $dueDate, $assignee)
    {
        $todo = new Todo($author, $title, $description, $dueDate, $assignee);

        $expectedPayload = json_encode([
            'id' => $todo->getId()->__toString(),
            'title' => $title,
            'description' => $description,
            'done' => false,
            'due_date' => $dueDate,
            'author_username' => $author->getUsername(),
            'assignee_username' => $assignee ? $assignee->getUsername() : '',
            'comments' => []
        ], JSON_PRETTY_PRINT);

        $this->assertEquals($expectedPayload, json_encode($todo,JSON_PRETTY_PRINT));

        $comment = 'Comment';
        $todo->addComment($author, $comment);

        $expectedPayload = json_encode([
            'id' => $todo->getId()->__toString(),
            'title' => $title,
            'description' => $description,
            'done' => false,
            'due_date' => $dueDate,
            'author_username' => $author->getUsername(),
            'assignee_username' => $assignee ? $assignee->getUsername() : '',
            'comments' => [
                [
                    'comment_author' => $author->getUsername(),
                    'comment' => $comment
                ]
            ]
        ], JSON_PRETTY_PRINT);

        $this->assertEquals($expectedPayload, json_encode($todo, JSON_PRETTY_PRINT));
    }
}
