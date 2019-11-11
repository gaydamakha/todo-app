<?php
declare(strict_types=1);

namespace App\Domain\Todo;

class AssignedTodoNotFoundException extends TodoNotFoundException
{
    public $message = 'This todo was not assigned to this user';
}
