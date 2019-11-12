<?php
declare(strict_types=1);

use App\Domain\Todo\TodoRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Todo\InMemoryTodoRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\ContainerBuilder;
use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => autowire(InMemoryUserRepository::class),
        TodoRepository::class => autowire(InMemoryTodoRepository::class),
    ]);
};
