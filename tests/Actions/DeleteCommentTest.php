<?php

namespace Tests\Actions;

use App\Http\Request;
use App\Http\ErrorResponse;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Http\SuccessfulResponse;
use App\Http\Actions\DeleteComment;
use App\Repositories\CommentRepository;
use App\Exceptions\CommentNotFoundException;
use App\Commands\DeleteCommentCommandHandler;

class DeleteCommentTest extends TestCase
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

        $deleteCommentCommandHandler = $this->createStub(DeleteCommentCommandHandler::class);
        $commentRepositoryStub = $this->createStub(CommentRepository::class);

        $action = new DeleteComment(
            $deleteCommentCommandHandler,
            $commentRepositoryStub,
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

        $deleteCommentCommandHandler = $this->createStub(DeleteCommentCommandHandler::class);
        $commentRepositoryStub = $this->createStub(CommentRepository::class);

        $action = new DeleteComment(
            $deleteCommentCommandHandler,
            $commentRepositoryStub,
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

        $deleteCommentCommandHandler = $this->createStub(DeleteCommentCommandHandler::class);
        $commentRepositoryStub = $this->createStub(CommentRepository::class);

        $action = new DeleteComment(
            $deleteCommentCommandHandler,
            $commentRepositoryStub,
            $this->getLogger(),
        );

        /**
         * @var Stub $commentRepositoryStub
         */
        $commentRepositoryStub->method('findById')->willThrowException(
            new CommentNotFoundException('Comment not found')
        );

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Comment not found"}'
        );

        $response->send();
    }
}
