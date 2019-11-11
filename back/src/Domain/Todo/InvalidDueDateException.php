<?php
declare(strict_types=1);

namespace App\Domain\Todo;

use App\Domain\DomainException\DomainInvalidValueException;

class InvalidDueDateException extends DomainInvalidValueException
{
    public $message = 'It\'s too late to create this Todo.';
}
