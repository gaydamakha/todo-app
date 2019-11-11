<?php
declare(strict_types=1);

namespace App\Domain\User;

use MongoDB\BSON\ObjectId;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param ObjectId $id
     * @return User
     */
    public function findUserOfId(ObjectId $id): User;
}
