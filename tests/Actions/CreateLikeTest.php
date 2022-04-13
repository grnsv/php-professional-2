<?php

namespace Tests\Actions;

use Faker\Factory;
use Faker\Generator;
use App\Http\Request;
use App\Entities\Like\Like;
use App\Entities\User\User;
use App\Http\ErrorResponse;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Http\Actions\CreateLike;
use App\Http\SuccessfulResponse;
use App\Entities\Article\Article;
use App\Repositories\ArticleRepository;
use App\Commands\CreateLikeCommandHandler;
use App\Http\Auth\TokenAuthenticationInterface;

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
        return
            [
                $this->getTestData(),
                $this->getTestData(),
                $this->getTestData(),
            ];
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider argumentsProvider
     */
    public function testItReturnsSuccessfulResponse($user, $article, $like): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"userId":"%d","articleId":"%d"}',
                $user->getId(),
                $article->getId(),
            )
        );

        $createLikeCommandHandlerStub = $this->createStub(CreateLikeCommandHandler::class);
        $tokenAuthenticationInterface = $this->createStub(TokenAuthenticationInterface::class);

        $action = new CreateLike(
            $createLikeCommandHandlerStub,
            $tokenAuthenticationInterface,
            $this->createStub(ArticleRepository::class),
            $this->getLogger(),
        );

        /**
         * @var Stub $tokenAuthenticationInterface
         */
        $tokenAuthenticationInterface->method('getUser')->willReturn($user);

        /**
         * @var Stub $createLikeCommandHandlerStub
         */
        $createLikeCommandHandlerStub->method('handle')->willReturn($like);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString(
            sprintf(
                '{"success":true,"data":{"id":%d,"userId":%d,"articleId":%d}}',
                $like->getId(),
                $user->getId(),
                $article->getId(),
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

        $action = new CreateLike(
            $this->createStub(CreateLikeCommandHandler::class),
            $this->createStub(TokenAuthenticationInterface::class),
            $this->createStub(ArticleRepository::class),
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
    public function testItReturnsErrorResponseIfNoArticleIdProvided($user): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"userId":"%d","articleId":""}',
                $user->getId(),
            )
        );

        $action = new CreateLike(
            $this->createStub(CreateLikeCommandHandler::class),
            $this->createStub(TokenAuthenticationInterface::class),
            $this->createStub(ArticleRepository::class),
            $this->getLogger(),
        );

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Empty field: articleId"}'
        );

        $response->send();
    }

    private function getTestData(): array
    {
        $user = new User(
            $this->faker->firstName(),
            $this->faker->lastName(),
            $this->faker->email(),
            $this->faker->password(),
        );
        $user->setId(mt_rand(1, mt_getrandmax()));

        $article = new Article(
            $user,
            $this->faker->title(),
            $this->faker->text(),
        );
        $article->setId(mt_rand(1, mt_getrandmax()));

        $like = new Like(
            $user,
            $article,
        );
        $like->setId(mt_rand(1, mt_getrandmax()));

        return [$user, $article, $like];
    }
}
