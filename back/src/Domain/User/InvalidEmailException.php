<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\DomainException\DomainInvalidValueException;

class InvalidEmailException extends DomainInvalidValueException
{
    public $message = 'The email you entered is invalid.';
}
