<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use MongoDB\BSON\ObjectId;

class InMemoryUserRepository implements UserRepository
{
    /**
     * @var User[]
     */
    private $users;

    /**
     * InMemoryUserRepository constructor.
     *
     * @param array|null $users
     */
    public function __construct(array $users = null)
    {
        $fakeUsers = new ArrayCollection(
            [
                new User((new ObjectId())->__toString(),'bill.gates@outlook.com', 'password1', 'Bill', 'Gates'),
                new User((new ObjectId())->__toString(),'steve.jobs@apple.com', 'password2', 'Steve', 'Jobs'),
                new User((new ObjectId())->__toString(),'mark.zuckerberg@facebook.com', 'password3', 'Mark', 'Zuckerberg'),
                new User((new ObjectId())->__toString(),'evan.spiegel@snap.com', 'password4', 'Evan', 'Spiegel'),
                new User((new ObjectId())->__toString(),'jack.dorsey@twitter.com', 'password5', 'Jack', 'Dorsey'),
            ]
        );

        $this->users = $users ? new ArrayCollection($users) : $fakeUsers;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return $this->users->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function findUserOfId(string $id): User
    {
        foreach ($this->users as $user) {
            if ($id === $user->getId()) {
                return $user;
            }
        }

        throw new UserNotFoundException();
    }

    /**
     * {@inheritdoc}
     */
    public function findUserOfUsername(string $username): User
    {
        $username = strtolower($username);
        foreach ($this->users as $user) {
            if ($username === $user->getUsername()) {
                return $user;
            }
        }

        throw new UserNotFoundException();
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     * @return User
     */
    public function createUser(string $username, string $password, string $firstName, string $lastName): User
    {
        // TODO: Implement createUser() method.
    }

    /**
     * @param string $username
     * @return void
     * @throws UserNotFoundException
     */
    public function deleteUser(string $username): User
    {
        // TODO: Implement deleteUser() method.
    }

    /**
     * @param string $apiToken
     * @return User
     */
    public function findUserByToken(string $apiToken)
    {
        // TODO: Implement findUserByToken() method.
    }
}
