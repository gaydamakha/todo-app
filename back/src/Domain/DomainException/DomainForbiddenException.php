<?php
declare(strict_types=1);

namespace App\Domain\DomainException;

class DomainForbiddenException extends DomainException
{
    public $message = "You're not authorized to perform this action.";
}