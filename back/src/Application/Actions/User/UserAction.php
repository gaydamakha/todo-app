<?php


namespace App\Application\Actions\User;

use App\Application\Actions\AbstractAction;
use App\Domain\User\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class UserAction extends AbstractAction
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    public function __construct(ValidatorInterface $validator, LoggerInterface $logger, UserRepository $userRepository)
    {
        parent::__construct($validator, $logger);
        $this->userRepository = $userRepository;
    }
}
