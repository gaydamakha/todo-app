<?php
declare(strict_types=1);

namespace App\Domain\Todo;

use App\Domain\DomainException\DomainInvalidValueException;

class InvalidAuthorOrAssigneeException extends DomainInvalidValueException
{
    public $message = 'You are not author or assignee, so toy have no right to interact with this todo.';
}
