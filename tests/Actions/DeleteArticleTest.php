<?php

namespace Tests\Actions;

use PDOStatement;
use App\Http\Request;
use App\Drivers\Connection;
use App\Http\ErrorResponse;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Http\SuccessfulResponse;
use App\Http\Actions\DeleteArticle;
use App\Repositories\ArticleRepository;
use App\Commands\DeleteArticleCommandHandler;

class DeleteArticleTest extends TestCase
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

        $deleteArticleCommandHandler = $this->createStub(DeleteArticleCommandHandler::class);

        /**
         * @var DeleteArticleCommandHandler $deleteArticleCommandHandler
         */
        $action = new DeleteArticle($deleteArticleCommandHandler, $this->getLogger());

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

        $deleteArticleCommandHandler = $this->createStub(DeleteArticleCommandHandler::class);

        /**
         * @var DeleteArticleCommandHandler $deleteArticleCommandHandler
         */
        $action = new DeleteArticle($deleteArticleCommandHandler, $this->getLogger());

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
         * @var Stub $articleRepositoryStub
         */
        $articleRepositoryStub = $this->createStub(ArticleRepository::class);
        $articleRepositoryStub->method('isExists')->willReturn(false);

        /**
         * @var ArticleRepository $articleRepositoryStub
         * @var Connection $connectionStub
         */
        $deleteArticleCommandHandler = new DeleteArticleCommandHandler(
            $articleRepositoryStub,
            $connectionStub,
            $this->getLogger(),
        );
        /**
         * @var DeleteArticleCommandHandler $deleteArticleCommandHandler
         */
        $action = new DeleteArticle($deleteArticleCommandHandler, $this->getLogger());

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Article not found"}'
        );

        $response->send();
    }
}
