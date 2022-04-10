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
use App\Http\Actions\CreateLike;
use App\Http\SuccessfulResponse;
use App\Entities\Article\Article;
use App\Repositories\UserRepository;
use App\Repositories\ArticleRepository;
use App\Commands\CreateLikeCommandHandler;
use App\Repositories\UserRepositoryInterface;
use App\Http\Auth\TokenAuthenticationInterface;
use App\Repositories\ArticleRepositoryInterface;

class CreateLikeTest extends TestCase
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
        $userId = mt_rand(1, mt_getrandmax());
        $articleId = mt_rand(1, mt_getrandmax());
        return
            [
                [$userId, $articleId],
            ];
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider argumentsProvider
     */
    public function testItReturnsSuccessfulResponse($userId, $articleId): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"userId":"%d","articleId":"%d"}',
                $userId,
                $articleId,
            )
        );

        $user = new User(
            $this->faker->userName(),
            $this->faker->word(),
            $this->faker->email(),
            $this->faker->password(),
        );
        $user->setId($userId);

        $article = new Article(
            $user,
            $this->faker->word(),
            $this->faker->text(),
        );
        $article->setId($articleId);

        $createLikeCommandHandlerStub = $this->createStub(CreateLikeCommandHandler::class);
        $tokenAuthenticationInterface = $this->createStub(TokenAuthenticationInterface::class);

        $action = new CreateLike(
            $createLikeCommandHandlerStub,
            $tokenAuthenticationInterface,
            $this->getLogger(),
        );

        /**
         * @var Stub $tokenAuthenticationInterface
         */
        $tokenAuthenticationInterface->method('getUser')->willReturn($user);

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
        $userRepositoryStub->method('get')->willReturn($user);

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
                '{"success":true,"data":{"userId":%d,"articleId":%d}}',
                $userId,
                $articleId,
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

        $createLikeCommandHandlerStub = $this->createStub(CreateLikeCommandHandler::class);

        /**
         * @var CreateLikeCommandHandler $createLikeCommandHandlerStub
         */
        $action = new CreateLike(
            $createLikeCommandHandlerStub,
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
    public function testItReturnsErrorResponseIfNoArticleIdProvided($userId): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"userId":"%d","articleId":""}',
                $userId,
            )
        );

        $createLikeCommandHandlerStub = $this->createStub(CreateLikeCommandHandler::class);

        /**
         * @var CreateLikeCommandHandler $createLikeCommandHandlerStub
         */
        $action = new CreateLike(
            $createLikeCommandHandlerStub,
            $this->createStub(TokenAuthenticationInterface::class),
            $this->getLogger(),
        );

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Empty field: articleId"}'
        );

        $response->send();
    }
}
