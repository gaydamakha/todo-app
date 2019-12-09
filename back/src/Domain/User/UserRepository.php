<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param string $id
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfId(string $id);

    /**
     * @param string $username
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfUsername(string $username);

    /**
     * @param string $username
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     * @return User
     */
    public function createUser(string $username, string $password, string $firstName, string $lastName): User;

    /**
     * @param string $username
     * @return User
     * @throws UserNotFoundException
     */
    public function deleteUser(string $username): User;
}
