<?php

namespace Tests\Actions;

use Faker\Factory;
use Faker\Generator;
use App\Http\Request;
use App\Http\ErrorResponse;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Http\SuccessfulResponse;
use App\Http\Actions\CreateArticle;
use App\Commands\CreateArticleCommandHandler;

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

    // /**
    //  * @runInSeparateProcess
    //  * @preserveGlobalState disabled
    //  * @dataProvider argumentsProvider
    //  */
    // public function testItReturnsSuccessfulResponse($authorId, $title, $text): void
    // {
    //     $request = new Request(
    //         [],
    //         [],
    //         sprintf(
    //             '{"authorId":"%d","title":"%s","text":"%s"}',
    //             $authorId,
    //             $title,
    //             $text,
    //         )
    //     );

    //     $createArticleCommandHandlerStub = $this->createStub(CreateArticleCommandHandler::class);

    //     /**
    //      * @var CreateArticleCommandHandler $createArticleCommandHandlerStub
    //      */
    //     $action = new CreateArticle($createArticleCommandHandlerStub);

    //     $response = $action->handle($request);

    //     $this->assertInstanceOf(SuccessfulResponse::class, $response);
    //     $this->expectOutputString(
    //         sprintf(
    //             '{"success":true,"data":{"authorId":"%d","title":"%s"}}',
    //             $authorId,
    //             $title,
    //         )
    //     );

    //     $response->send();
    // }

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
        $action = new CreateArticle($createArticleCommandHandlerStub, $this->getLogger());

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
        $action = new CreateArticle($createArticleCommandHandlerStub, $this->getLogger());

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Empty field: text"}'
        );

        $response->send();
    }
}
