<?php
declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use MongoDB\BSON\ObjectId;
use Tests\TestCase;

class InMemoryUserRepositoryTest extends TestCase
{
    public function testFindAll()
    {
        $user = new User('bill.gates@outlook.com', 'password', 'Bill', 'Gates');

        $userRepository = new InMemoryUserRepository([$user]);

        $this->assertEquals([$user], $userRepository->findAll());
    }

    public function testFindUserOfId()
    {
        $user = new User('bill.gates@outlook.com', 'password', 'Bill', 'Gates');

        $id = $user->getId();
        $userRepository = new InMemoryUserRepository([$user]);

        $this->assertEquals($user, $userRepository->findUserOfId($id));
    }

    /**
     * @expectedException \App\Domain\User\UserNotFoundException
     */
    public function testFindUserOfIdThrowsNotFoundException()
    {
        $userRepository = new InMemoryUserRepository([]);
        $userRepository->findUserOfId(new ObjectId());
    }
}
