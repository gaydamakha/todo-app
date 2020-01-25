<?php
declare(strict_types=1);

namespace App\Domain\DomainException;

class DomainInvalidValueException extends DomainException
{
    public $message = "Invalid value provided.";
}