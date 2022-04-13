<?php

namespace Tests\Actions;

use Faker\Factory;
use Faker\Generator;
use App\Http\Request;
use App\Entities\User\User;
use App\Http\ErrorResponse;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Http\SuccessfulResponse;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Http\Actions\CreateComment;
use App\Repositories\ArticleRepository;
use App\Commands\CreateCommentCommandHandler;
use App\Http\Auth\TokenAuthenticationInterface;

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
    public function testItReturnsSuccessfulResponse($user, $article, $comment): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"authorId":"%d","articleId":"%d","text":"%s"}',
                $user->getId(),
                $article->getId(),
                $comment->getText(),
            )
        );

        $createCommentCommandHandlerStub = $this->createStub(CreateCommentCommandHandler::class);
        $tokenAuthenticationInterface = $this->createStub(TokenAuthenticationInterface::class);

        $action = new CreateComment(
            $createCommentCommandHandlerStub,
            $tokenAuthenticationInterface,
            $this->createStub(ArticleRepository::class),
            $this->getLogger(),
        );

        /**
         * @var Stub $tokenAuthenticationInterface
         */
        $tokenAuthenticationInterface->method('getUser')->willReturn($user);

        /**
         * @var Stub $createCommentCommandHandlerStub
         */
        $createCommentCommandHandlerStub->method('handle')->willReturn($comment);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString(
            sprintf(
                '{"success":true,"data":{"id":%d,"authorId":%d,"articleId":%d,"text":"%s"}}',
                $comment->getId(),
                $user->getId(),
                $article->getId(),
                $comment->getText(),
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

        $action = new CreateComment(
            $this->createStub(CreateCommentCommandHandler::class),
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
    public function testItReturnsErrorResponseIfNoTextProvided($user, $article): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"authorId":"%d","articleId":"%s","text":""}',
                $user->getId(),
                $article->getId(),
            )
        );

        $action = new CreateComment(
            $this->createStub(CreateCommentCommandHandler::class),
            $this->createStub(TokenAuthenticationInterface::class),
            $this->createStub(ArticleRepository::class),
            $this->getLogger(),
        );

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Empty field: text"}'
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

        $comment = new Comment(
            $user,
            $article,
            $this->faker->text(),
        );
        $comment->setId(mt_rand(1, mt_getrandmax()));

        return [$user, $article, $comment];
    }
}
