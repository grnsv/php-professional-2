<?php

namespace Tests\Actions;

use App\Http\Request;
use App\Http\ErrorResponse;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Http\SuccessfulResponse;
use App\Http\Actions\DeleteArticle;
use App\Repositories\ArticleRepository;
use App\Exceptions\ArticleNotFoundException;
use App\Commands\DeleteArticleCommandHandler;

class DeleteArticleTest extends TestCase
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

        $deleteArticleCommandHandler = $this->createStub(DeleteArticleCommandHandler::class);
        $articleRepositoryStub = $this->createStub(ArticleRepository::class);

        $action = new DeleteArticle(
            $deleteArticleCommandHandler,
            $articleRepositoryStub,
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

        $deleteArticleCommandHandler = $this->createStub(DeleteArticleCommandHandler::class);
        $articleRepositoryStub = $this->createStub(ArticleRepository::class);

        $action = new DeleteArticle(
            $deleteArticleCommandHandler,
            $articleRepositoryStub,
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

        $deleteArticleCommandHandler = $this->createStub(DeleteArticleCommandHandler::class);
        $articleRepositoryStub = $this->createStub(ArticleRepository::class);

        $action = new DeleteArticle(
            $deleteArticleCommandHandler,
            $articleRepositoryStub,
            $this->getLogger(),
        );

        /**
         * @var Stub $articleRepositoryStub
         */
        $articleRepositoryStub->method('findById')->willThrowException(
            new ArticleNotFoundException('Article not found')
        );

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Article not found"}'
        );

        $response->send();
    }
}
