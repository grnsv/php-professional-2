<?php

namespace Tests\Actions;

use App\Http\Request;
use App\Http\ErrorResponse;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Http\Actions\DeleteUser;
use App\Http\SuccessfulResponse;
use App\Repositories\UserRepository;
use App\Exceptions\UserNotFoundException;
use App\Commands\DeleteUserCommandHandler;

class DeleteUserTest extends TestCase
{
    use LoggerTrait;

    public function argumentsProvider(): iterable
    {
        return
            [
                [mt_rand(1, mt_getrandmax())],
                [mt_rand(1, mt_getrandmax())],
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
        $userRepositoryStub = $this->createStub(UserRepository::class);

        $action = new DeleteUser(
            $deleteUserCommandHandler,
            $userRepositoryStub,
            $this->getLogger(),
        );

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
        $userRepositoryStub = $this->createStub(UserRepository::class);

        $action = new DeleteUser(
            $deleteUserCommandHandler,
            $userRepositoryStub,
            $this->getLogger(),
        );

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

        $deleteUserCommandHandler = $this->createStub(DeleteUserCommandHandler::class);
        $userRepositoryStub = $this->createStub(UserRepository::class);

        $action = new DeleteUser(
            $deleteUserCommandHandler,
            $userRepositoryStub,
            $this->getLogger(),
        );

        /**
         * @var Stub $userRepositoryStub
         */
        $userRepositoryStub->method('findById')->willThrowException(
            new UserNotFoundException('User not found')
        );

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"User not found"}'
        );

        $response->send();
    }
}
