<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\DomainException\DomainInvalidValueException;

class UserAlreadyExistsException extends DomainInvalidValueException
{
    public $message = 'The username you entered is already taken.';
}
