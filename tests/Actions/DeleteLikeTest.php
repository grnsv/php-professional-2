<?php

namespace Tests\Actions;

use PDOStatement;
use App\Http\Request;
use App\Drivers\Connection;
use App\Http\ErrorResponse;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Http\Actions\DeleteLike;
use App\Http\SuccessfulResponse;
use App\Repositories\LikeRepository;
use App\Commands\DeleteLikeCommandHandler;

class DeleteLikeTest extends TestCase
{
    use LoggerTrait;

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

        $deleteLikeCommandHandler = $this->createStub(DeleteLikeCommandHandler::class);

        /**
         * @var DeleteLikeCommandHandler $deleteLikeCommandHandler
         */
        $action = new DeleteLike($deleteLikeCommandHandler, $this->getLogger());

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

        $deleteLikeCommandHandler = $this->createStub(DeleteLikeCommandHandler::class);

        /**
         * @var DeleteLikeCommandHandler $deleteLikeCommandHandler
         */
        $action = new DeleteLike($deleteLikeCommandHandler, $this->getLogger());

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
         * @var Stub $likeRepositoryStub
         */
        $likeRepositoryStub = $this->createStub(LikeRepository::class);
        $likeRepositoryStub->method('isExists')->willReturn(false);

        /**
         * @var LikeRepository $likeRepositoryStub
         * @var Connection $connectionStub
         */
        $deleteLikeCommandHandler = new DeleteLikeCommandHandler(
            $likeRepositoryStub,
            $connectionStub,
            $this->getLogger(),
        );
        /**
         * @var DeleteLikeCommandHandler $deleteLikeCommandHandler
         */
        $action = new DeleteLike($deleteLikeCommandHandler, $this->getLogger());

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Like not found"}'
        );

        $response->send();
    }
}
