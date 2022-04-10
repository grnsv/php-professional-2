<?php

namespace Tests\Actions;

use PDOStatement;
use Faker\Factory;
use Faker\Generator;
use App\Http\Request;
use App\Drivers\Connection;
use App\Entities\User\User;
use App\Http\ErrorResponse;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Http\SuccessfulResponse;
use App\Entities\Article\Article;
use App\Http\Actions\CreateComment;
use App\Repositories\UserRepository;
use App\Repositories\ArticleRepository;
use App\Commands\CreateCommentCommandHandler;
use App\Repositories\UserRepositoryInterface;
use App\Http\Auth\TokenAuthenticationInterface;
use App\Repositories\ArticleRepositoryInterface;

class CreateCommentTest extends TestCase
{
    use LoggerTrait;

    private Generator $faker;

    public function __construct(
        ?string $name = null,
        array $data = [],
        $dataName = '',
    ) {
        $this->faker = Factory::create();
        parent::__construct($name, $data, $dataName);
    }

    public function argumentsProvider(): iterable
    {
        return
            [
                [mt_rand(1, mt_getrandmax()), mt_rand(1, mt_getrandmax()), $this->faker->text()],
            ];
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider argumentsProvider
     */
    public function testItReturnsSuccessfulResponse($authorId, $articleId, $text): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"authorId":"%d","articleId":"%d","text":"%s"}',
                $authorId,
                $articleId,
                $text,
            )
        );

        $author = new User(
            $this->faker->userName(),
            $this->faker->word(),
            $this->faker->email(),
            $this->faker->password(),
        );
        $author->setId($authorId);

        $article = new Article(
            $author,
            $this->faker->word(),
            $this->faker->text(),
        );
        $article->setId($articleId);

        $createCommentCommandHandlerStub = $this->createStub(CreateCommentCommandHandler::class);
        $tokenAuthenticationInterface = $this->createStub(TokenAuthenticationInterface::class);

        $action = new CreateComment(
            $createCommentCommandHandlerStub,
            $tokenAuthenticationInterface,
            $this->getLogger(),
        );

        /**
         * @var Stub $tokenAuthenticationInterface
         */
        $tokenAuthenticationInterface->method('getUser')->willReturn($author);

        $userRepositoryStub = $this->createStub(UserRepository::class);
        $articleRepositoryStub = $this->createStub(ArticleRepository::class);
        $connectionStub = $this->createStub(Connection::class);
        $statementStub = $this->createStub(PDOStatement::class);

        /**
         * @var DIContainer @container
         */
        $container = $this->getContainer();
        $container->bind(
            UserRepositoryInterface::class,
            $userRepositoryStub
        );
        $container->bind(
            UserRepository::class,
            $userRepositoryStub
        );
        $container->bind(
            ArticleRepositoryInterface::class,
            $articleRepositoryStub
        );
        $container->bind(
            ArticleRepository::class,
            $articleRepositoryStub
        );
        $container->bind(
            Connection::class,
            $connectionStub
        );

        /**
         * @var Stub $userRepositoryStub
         */
        $userRepositoryStub->method('get')->willReturn($author);

        /**
         * @var Stub $articleRepositoryStub
         */
        $articleRepositoryStub->method('get')->willReturn($article);

        /**
         * @var Stub $connectionStub
         */
        $connectionStub->method('prepare')->willReturn($statementStub);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString(
            sprintf(
                '{"success":true,"data":{"authorId":%d,"articleId":%d,"text":"%s"}}',
                $authorId,
                $articleId,
                $text,
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

        $createCommentCommandHandlerStub = $this->createStub(CreateCommentCommandHandler::class);

        /**
         * @var CreateCommentCommandHandler $createCommentCommandHandlerStub
         */
        $action = new CreateComment(
            $createCommentCommandHandlerStub,
            $this->createStub(TokenAuthenticationInterface::class),
            $this->getLogger(),
        );

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Cannot decode json body"}'
        );

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider argumentsProvider
     */
    public function testItReturnsErrorResponseIfNoTextProvided($authorId, $articleId): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"authorId":"%d","articleId":"%s","text":""}',
                $authorId,
                $articleId,
            )
        );

        $createCommentCommandHandlerStub = $this->createStub(CreateCommentCommandHandler::class);

        /**
         * @var CreateCommentCommandHandler $createCommentCommandHandlerStub
         */
        $action = new CreateComment(
            $createCommentCommandHandlerStub,
            $this->createStub(TokenAuthenticationInterface::class),
            $this->getLogger(),
        );

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Empty field: text"}'
        );

        $response->send();
    }
}
