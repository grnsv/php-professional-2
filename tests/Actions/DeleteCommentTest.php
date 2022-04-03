<?php

namespace Tests;

use PDOStatement;
use App\Http\Request;
use App\Drivers\Connection;
use App\Http\ErrorResponse;
use PHPUnit\Framework\TestCase;
use App\Http\SuccessfulResponse;
use App\Http\Actions\DeleteComment;
use App\Repositories\CommentRepository;
use App\Commands\DeleteCommentCommandHandler;

class DeleteCommentTest extends TestCase
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

        $deleteCommentCommandHandler = $this->createStub(DeleteCommentCommandHandler::class);

        /**
         * @var DeleteCommentCommandHandler $deleteCommentCommandHandler
         */
        $action = new DeleteComment($deleteCommentCommandHandler);

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

        $deleteCommentCommandHandler = $this->createStub(DeleteCommentCommandHandler::class);

        /**
         * @var DeleteCommentCommandHandler $deleteCommentCommandHandler
         */
        $action = new DeleteComment($deleteCommentCommandHandler);

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
         * @var Stub $commentRepositoryStub
         */
        $commentRepositoryStub = $this->createStub(CommentRepository::class);
        $commentRepositoryStub->method('isExists')->willReturn(false);

        /**
         * @var CommentRepository $commentRepositoryStub
         * @var Connection $connectionStub
         */
        $deleteCommentCommandHandler = new DeleteCommentCommandHandler(
            $commentRepositoryStub,
            $connectionStub,
        );
        /**
         * @var DeleteCommentCommandHandler $deleteCommentCommandHandler
         */
        $action = new DeleteComment($deleteCommentCommandHandler);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Comment not found"}'
        );

        $response->send();
    }
}
