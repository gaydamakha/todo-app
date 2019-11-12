<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\InvalidEmailException;
use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
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
     * @throws InvalidEmailException
     */
    public function __construct(array $users = null)
    {
        $fakeUsers = [
            new User('bill.gates@outlook.com', 'password1', 'Bill', 'Gates'),
            new User('steve.jobs@apple.com', 'password2', 'Steve', 'Jobs'),
            new User('mark.zuckerberg@facebook.com', 'password3', 'Mark', 'Zuckerberg'),
            new User('evan.spiegel@snap.com', 'password4', 'Evan', 'Spiegel'),
            new User('jack.dorsey@twitter.com', 'password5', 'Jack', 'Dorsey'),
        ];

        $this->users = $users ?? $fakeUsers;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return $this->users;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserOfId(ObjectId $id): User
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
}
