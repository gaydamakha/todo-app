<?php
declare(strict_types=1);

namespace App\Domain\Todo;

class CreatedTodoNotFoundException extends TodoNotFoundException
{
    public $message = 'This todo was not created by this user.';
}
