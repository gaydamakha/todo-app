<?php
declare(strict_types=1);

namespace App\Application\Actions\Todo;

use App\Application\Actions\AbstractAction;
use App\Domain\Todo\TodoRepository;
use App\Domain\User\UserRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;

abstract class TodoAction extends AbstractAction
{
    /**
     * @var TodoRepository
     */
    protected $todoRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @param LoggerInterface $logger
     * @param Validator $validator
     * @param TodoRepository $todoRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        ValidatorInterface $validator,
        LoggerInterface $logger,
        TodoRepository $todoRepository,
        UserRepository $userRepository
    )
    {
        parent::__construct($validator, $logger);
        $this->todoRepository = $todoRepository;
        $this->userRepository = $userRepository;
    }
}
