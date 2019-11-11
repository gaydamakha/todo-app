<?php
declare(strict_types=1);

namespace App\Domain\Todo;

use App\Domain\DomainException\DomainInvalidValueException;

class InvalidAuthorException extends DomainInvalidValueException
{
    public $message = 'The author of Todo is invalid.';
}
