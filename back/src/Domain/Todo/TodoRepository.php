<?php
declare(strict_types=1);

namespace App\Domain\Todo;

use MongoDB\BSON\ObjectId;

interface TodoRepository
{
    /**
     * @return Todo[]
     */
    public function findAll(): array;

    /**
     * @param ObjectId $id
     * @return Todo
     */
    public function findTodoOfId(ObjectId $id): Todo;
}
