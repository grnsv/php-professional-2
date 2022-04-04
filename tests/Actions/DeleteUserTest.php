<?php

namespace Tests;

use PDOStatement;
use App\Http\Request;
use App\Drivers\Connection;
use App\Http\ErrorResponse;
use PHPUnit\Framework\TestCase;
use App\Http\Actions\DeleteUser;
use App\Http\SuccessfulResponse;
use App\Repositories\UserRepository;
use App\Commands\DeleteUserCommandHandler;

class DeleteUserTest extends TestCase
{
    public function argumentsProvider(): iterable
    {
        return
            [
                [mt_rand(1, mt_getrandmax())],
            ];
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider argumentsProvider
     */
    public function testItReturnsSuccessfulResponse($id): void
    {
        $request = new Request(['id' => $id], [], '');

        $deleteUserCommandHandler = $this->createStub(DeleteUserCommandHandler::class);

        /**
         * @var DeleteUserCommandHandler $deleteUserCommandHandler
         */
        $action = new DeleteUser($deleteUserCommandHandler);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString(
            sprintf(
                '{"success":true,"data":{"id":"%s"}}',
                $id,
            )
        );

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfNoDataProvided(): void
    {
        $request = new Request([], [], '');

        $deleteUserCommandHandler = $this->createStub(DeleteUserCommandHandler::class);

        /**
         * @var DeleteUserCommandHandler $deleteUserCommandHandler
         */
        $action = new DeleteUser($deleteUserCommandHandler);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"No such query param in the request: id"}'
        );

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider argumentsProvider
     */
    public function testItReturnsErrorResponseIfIdNotFound($id): void
    {
        $request = new Request(['id' => $id], [], '');

        /**
         * @var Stub $connectionStub
         */
        $connectionStub = $this->createStub(Connection::class);
        $connectionStub->method('prepare')->willReturn(
            $this->createStub(PDOStatement::class)
        );
        /**
         * @var Stub $userRepositoryStub
         */
        $userRepositoryStub = $this->createStub(UserRepository::class);
        $userRepositoryStub->method('isUserExists')->willReturn(false);

        /**
         * @var UserRepository $userRepositoryStub
         * @var Connection $connectionStub
         */
        $deleteUserCommandHandler = new DeleteUserCommandHandler(
            $userRepositoryStub,
            $connectionStub,
        );
        /**
         * @var DeleteUserCommandHandler $deleteUserCommandHandler
         */
        $action = new DeleteUser($deleteUserCommandHandler);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"User not found"}'
        );

        $response->send();
    }
}
