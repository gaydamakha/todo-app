<?php


namespace Application\Actions\Todo;


use App\Application\Actions\ActionPayload;
use App\Domain\Todo\Todo;
use App\Domain\Todo\TodoRepository;
use App\Domain\User\User;
use DateTime;
use DI\Container;
use Tests\TestCase;

class ListTodosActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $author = new User('bill.gates@outlook.com', 'password', 'Bill', 'Gates');
        $dueDate = new DateTime();
        $dueDate->add(new \DateInterval('P1M'));
        $todo = new Todo($author, 'Cool task', 'Need to do this entirely', $dueDate, $author);

        $todoRepositoryProphecy = $this->prophesize(TodoRepository::class);
        $todoRepositoryProphecy
            ->findAll()
            ->willReturn([$todo])
            ->shouldBeCalledOnce();

        $container->set(TodoRepository::class, $todoRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/todos');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, [$todo]);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
