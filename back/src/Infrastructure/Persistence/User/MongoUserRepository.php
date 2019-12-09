<?php


namespace App\Infrastructure\Persistence\User;

use App\Domain\User;
use App\Domain\User\UserAlreadyExistsException;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\MongoRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;

class MongoUserRepository extends MongoRepository implements UserRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct($dm, User::class);
    }

    /**
     * @param string $id
     * @return User|object
     * @throws UserNotFoundException
     */
    public function findUserOfId(string $id)
    {
        $user = $this->find($id);
        if (null === $user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * @param string $username
     * @return User|object
     * @throws UserNotFoundException
     */
    public function findUserOfUsername(string $username)
    {
        $username = strtolower($username);

        $user = $this->findOneBy(["username" => $username]);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     * @return User
     * @throws UserAlreadyExistsException
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function createUser(string $username, string $password, string $firstName, string $lastName): User
    {
        //Try to find a user with the same username
        try {
            $this->findUserOfUsername(strtolower($username));
        } catch (UserNotFoundException $e) {
            //If an exception above is thrown, then the user does not exist => create new
            $user = new User((string)(new ObjectId()), $username, $password, $firstName, $lastName);
            $this->dm->persist($user);
            $this->dm->flush();
            return $user;
        }
        //Else, say that the user already exists
        throw new UserAlreadyExistsException();
    }

    /**
     * @param string $username
     * @return User
     * @throws UserNotFoundException
     */
    public function deleteUser(string $username): User
    {
        $user = $this->findUserOfUsername(strtolower($username));
        foreach ($user->getAssignedTodos() as $assignedTodo) {
            $user->removeAssignedTodo($assignedTodo, $user);
        }
        $this->dm->remove($user);
        $this->dm->flush();
        return $user;
    }
}
