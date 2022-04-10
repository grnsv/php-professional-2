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
use App\Container\DIContainer;
use PHPUnit\Framework\TestCase;
use App\Http\SuccessfulResponse;
use App\Http\Actions\CreateArticle;
use App\Repositories\UserRepository;
use App\Commands\CreateArticleCommandHandler;
use App\Repositories\UserRepositoryInterface;
use App\Http\Auth\TokenAuthenticationInterface;

class CreateArticleTest extends TestCase
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
                [mt_rand(1, mt_getrandmax()), $this->faker->word(), $this->faker->text()],
            ];
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider argumentsProvider
     */
    public function testItReturnsSuccessfulResponse($authorId, $title, $text): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"authorId":"%d","title":"%s","text":"%s"}',
                $authorId,
                $title,
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

        $createArticleCommandHandlerStub = $this->createStub(CreateArticleCommandHandler::class);
        $tokenAuthenticationInterface = $this->createStub(TokenAuthenticationInterface::class);

        $action = new CreateArticle(
            $createArticleCommandHandlerStub,
            $tokenAuthenticationInterface,
            $this->getLogger(),
        );

        /**
         * @var Stub $tokenAuthenticationInterface
         */
        $tokenAuthenticationInterface->method('getUser')->willReturn($author);

        $userRepositoryStub = $this->createStub(UserRepository::class);
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
            Connection::class,
            $connectionStub
        );

        /**
         * @var Stub $userRepositoryStub
         */
        $userRepositoryStub->method('get')->willReturn($author);

        /**
         * @var Stub $connectionStub
         */
        $connectionStub->method('prepare')->willReturn($statementStub);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString(
            sprintf(
                '{"success":true,"data":{"authorId":%d,"title":"%s","text":"%s"}}',
                $authorId,
                $title,
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

        $createArticleCommandHandlerStub = $this->createStub(CreateArticleCommandHandler::class);

        /**
         * @var CreateArticleCommandHandler $createArticleCommandHandlerStub
         */
        $action = new CreateArticle(
            $createArticleCommandHandlerStub,
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
    public function testItReturnsErrorResponseIfNoTextProvided($authorId, $title): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"authorId":"%d","title":"%s","text":""}',
                $authorId,
                $title,
            )
        );

        $createArticleCommandHandlerStub = $this->createStub(CreateArticleCommandHandler::class);

        /**
         * @var CreateArticleCommandHandler $createArticleCommandHandlerStub
         */
        $action = new CreateArticle(
            $createArticleCommandHandlerStub,
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
