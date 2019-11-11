<?php
declare(strict_types=1);

namespace Tests\Domain\User;

use App\Domain\Todo\Todo;
use App\Domain\User\User;
use DateTime;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function validUserProvider()
    {
        return [
            ['bill.gates@outlook.com', 'password1', 'Bill', 'Gates'],
            ['steve.jobs@aPPle.com', 'password2', 'Steve', 'Jobs'],
            ['mark.zuckerberg@facebook.com', 'password3', 'Mark', 'Zuckerberg'],
            ['EVAN.SPIEGEL@snap.com', 'password4', 'Evan', 'Spiegel'],
            ['JaCk.DoRsEy@twitter.com', 'password5', 'Jack', 'Dorsey'],
        ];
    }

    public function notValidUserProvider()
    {
        return [
            ['bill.gates', 'password1', 'Bill', 'Gates'],
            ['steve.jobs@apple', 'password2', 'Steve', 'Jobs'],
            ['@facebook.com', 'password3', 'Mark', 'Zuckerberg'],
        ];
    }

    public function authorAssigneeProvider(){
        $author = new User('author@mail.ru','password', 'author', 'notmatter');
        $assignee = new User('assignee@gmail.com', 'password2', 'Assignee', 'Worker');

        return [
            [$author, $author],
            [$author, $assignee]
        ];
    }

    //TODO: write a test for valid password (for authentication)

    /**
     * Checks if constructor is correct
     *
     * @dataProvider validUserProvider
     *
     * @param $username
     * @param $password
     * @param $firstName
     * @param $lastName
     */
    public function testGetters($username, $password, $firstName, $lastName)
    {
        $user = new User($username, $password, $firstName, $lastName);

        $this->assertEquals(strtolower($username), $user->getUsername());
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals($firstName, $user->getFirstName());
        $this->assertEquals($lastName, $user->getLastName());
    }

    /**
     * Checks if it's impossible to create a user with invalid email
     *
     * @dataProvider notValidUserProvider
     *
     * @expectedException \App\Domain\User\InvalidEmailException
     *
     * @param $invalidUsername
     * @param $password
     * @param $firstName
     * @param $lastName
     */
    public function testEmailValidity($invalidUsername, $password, $firstName, $lastName)
    {
        new User($invalidUsername, $password, $firstName, $lastName);
    }

    /**
     * Checks if the Todo can be created for given user
     */
    public function testAddCreatedTodo()
    {
        $author = new User('author@mail.ru','password', 'author', 'notmatter');

        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo = new Todo($author, 'todo1', 'first todo', $dueDate, null);

        $this->assertEquals(
            [$todo],
            $author->getCreatedTodos()
        );
    }

    /**
     * Checks if it's impossible to became an author of the Todo
     * In fact the only way to became an author of Todo
     * is to pass the user in the Todo controller
     *
     * @expectedException \App\Domain\Todo\InvalidAuthorException
     */
    public function testAddCreatedTodoWithOtherAuthor()
    {
        $author = new User('author@mail.ru','password', 'author', 'notmatter');
        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo = new Todo($author, 'todo1', 'first todo', $dueDate, null);

        $notAuthor = new User('notauthor@gmail.com','password', 'notauthor', 'matter');
        $notAuthor->addCreatedTodo($todo);
    }

    /**
     * Checks if it's possible to remove the Todo completely
     */
    public function testRemoveCreatedTodo()
    {
        $author = new User('author@mail.ru','password', 'author', 'notmatter');
        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo = new Todo($author, 'todo1', 'first todo', $dueDate, null);

        $author->removeCreatedTodo($todo->getId());
        $this->assertEmpty($author->getCreatedTodos());
    }

    /**
     * Checks if it's impossible to remove a todo created by other user
     *
     * @expectedException \App\Domain\Todo\CreatedTodoNotFoundException
     */
    public function testRemoveTodoOfOtherAuthor()
    {
        $author = new User('author@mail.ru','password', 'author', 'notmatter');
        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo = new Todo($author, 'todo1', 'first todo', $dueDate, null);

        $notAuthor = new User('notauthor@gmail.com','password', 'notauthor', 'matter');
        $notAuthor->removeCreatedTodo($todo->getId());
    }

    /**
     * Checks if it's possible to assign a Todo
     *
     * @dataProvider authorAssigneeProvider
     *
     * @param User $author
     * @param User $assignee
     */
    public function testAssignTodo(User $author, User $assignee)
    {
        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo = new Todo($author, 'todo1', 'first todo', $dueDate, $assignee);

        $this->assertEquals(
            [$todo],
            $assignee->getAssignedTodos()
        );
    }

    /**
     * Checks if it;s possible to re-assign a Todo
     * in behalf of the assignee
     */
    public function testReassignTodo()
    {
        $author = new User('author@mail.ru','password', 'author', 'notmatter');
        $assignee = new User('assignee@gmail.com', 'password2', 'Assignee', 'Worker');
        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo = new Todo($author, 'todo1', 'first todo', $dueDate, $assignee);
        //re-assign test
        $assignee->assignTodo($todo, $author);

        $this->assertEmpty($assignee->getAssignedTodos());
        $this->assertEquals(
            [$todo],
            $author->getAssignedTodos()
        );
    }

    /**
     * Checks if it's possible to re-assign the Todo
     * in behalf of author
     * to the author himself
     * without having a duplicate
     */
    public function testReassignTodoToSameUser()
    {
        $author = new User('author@mail.ru','password', 'author', 'notmatter');
        $assignee = new User('assignee@gmail.com', 'password2', 'Assignee', 'Worker');
        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo = new Todo($author, 'todo1', 'first todo', $dueDate, $assignee);
        //re-assign test
        $author->assignTodo($todo, $author);

        $this->assertEquals(
            [$todo],
            $author->getAssignedTodos()
        );
    }

    /**
     * Checks if it's impossible to re-assign a Todo
     * in behalf of third person
     *
     * @dataProvider authorAssigneeProvider
     *
     * @expectedException \App\Domain\Todo\InvalidAuthorOrAssigneeException
     *
     * @param User $author
     * @param User $assignee
     */
    public function testAssignTodoWithInvalidUser(User $author, User $assignee)
    {
        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo = new Todo($author, 'todo1', 'first todo', $dueDate, $assignee);

        $notAuthorNorAssignee = new User('notassignee@gmail.com','password', 'Notassignee', 'Matthew');
        $notAuthorNorAssignee->assignTodo($todo, $notAuthorNorAssignee);
    }

    /**
     * Checks if the assignee can des-assign from a Todo
     *
     * @dataProvider authorAssigneeProvider
     *
     * @param User $author
     * @param User $assignee
     */
    public function testRemoveAssignedTodoByAssignee(User $author, User $assignee)
    {
        $this->assertEmpty($assignee->getAssignedTodos());
        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo = new Todo($author, 'todo1', 'first todo', $dueDate, $assignee);

        $assignee->removeAssignedTodo($todo->getId(), $assignee);
        $this->assertEmpty($assignee->getAssignedTodos());
    }

    /**
     * Checks if the author can remove the assignee for a Todo
     *
     * @dataProvider authorAssigneeProvider
     *
     * @param User $author
     * @param User $assignee
     */
    public function testRemoveAssignedTodoByAuthor(User $author, User $assignee)
    {
        $this->assertEmpty($assignee->getAssignedTodos());
        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo = new Todo($author, 'todo1', 'first todo', $dueDate, $assignee);

        $author->removeAssignedTodo($todo->getId(), $assignee);
        $this->assertEmpty($assignee->getAssignedTodos());
    }

    /**
     * Checks if it's impossible to remove todo which doesn't exist
     *
     * @dataProvider authorAssigneeProvider
     *
     * @expectedException \App\Domain\Todo\AssignedTodoNotFoundException
     *
     * @param User $author
     * @param User $assignee
     *
     * @throws \App\Domain\User\InvalidEmailException
     */
    public function testRemoveNotExistingTodo(User $author, User $assignee)
    {
        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo = new Todo($author, 'todo1', 'first todo', $dueDate, $assignee);

        $notAssignee = new User('notassignee@gmail.com','password', 'Notassignee', 'Matthew');
        $notAssignee->removeAssignedTodo($todo->getId(), $notAssignee);
    }

    /**
     * Checks if it's impossible to de-assign a Todo
     * in behalf of third person
     *
     * @dataProvider authorAssigneeProvider
     *
     * @expectedException \App\Domain\Todo\InvalidAuthorOrAssigneeException
     *
     * @param User $author
     * @param User $assignee
     */
    public function testRemoveTodoOfOtherAssignee(User $author, User $assignee)
    {
        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo = new Todo($author, 'todo1', 'first todo', $dueDate, $assignee);

        $notAssignee = new User('notassignee@gmail.com','password', 'Notassignee', 'Matthew');
        $notAssignee->removeAssignedTodo($todo->getId(), $assignee);
    }

    /**
     * Checks for valid Json serialization
     *
     * @dataProvider validUserProvider
     *
     * @param $username
     * @param $password
     * @param $firstName
     * @param $lastName
     */
    public function testJsonSerialize($username, $password, $firstName, $lastName)
    {
        $user = new User($username, $password, $firstName, $lastName);
        $assignee = new User('bill@gmail.com', '123456789', 'bill', 'geits');
        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo1 = new Todo($user, 'todo1', 'first todo', $dueDate, $user);
        $todo2 = new Todo($user, 'todo2', 'second todo', $dueDate, $assignee);

        $createdTodos = [$todo1, $todo2];
        $assignedTodos = [$todo1];
        $expectedPayload = json_encode([
            'id' => $user->getId()->__toString(),
            'username' => strtolower($username),
            'firstName' => $firstName,
            'lastName' => $lastName,
            'created_todos' => $createdTodos,
            'assigned_todos' => $assignedTodos
        ]);

        $this->assertEquals($expectedPayload, json_encode($user));

        $expectedPayloadAssignee = json_encode([
            'id' => $assignee->getId()->__toString(),
            'username' => 'bill@geits.com',
            'firstName' => 'bill',
            'lastName' => 'geits',
            'created_todos' => [],
            'assigned_todos' => [$todo2]
        ]);

        $this->assertEquals($expectedPayloadAssignee, json_encode($assignee));
    }
}
