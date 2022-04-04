<?php

namespace Tests;

use Faker\Factory;
use Faker\Generator;
use App\Http\Request;
use App\Http\ErrorResponse;
use PHPUnit\Framework\TestCase;
use App\Http\SuccessfulResponse;
use App\Http\Actions\CreateComment;
use App\Commands\CreateCommentCommandHandler;

class CreateCommentTest extends TestCase
{
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

    // /**
    //  * @runInSeparateProcess
    //  * @preserveGlobalState disabled
    //  * @dataProvider argumentsProvider
    //  */
    // public function testItReturnsSuccessfulResponse($authorId, $articleId, $text): void
    // {
    //     $request = new Request(
    //         [],
    //         [],
    //         sprintf(
    //             '{"authorId":"%d","articleId":"%d","text":"%s"}',
    //             $authorId,
    //             $articleId,
    //             $text,
    //         )
    //     );

    //     $createCommentCommandHandlerStub = $this->createStub(CreateCommentCommandHandler::class);

    //     /**
    //      * @var CreateCommentCommandHandler $createCommentCommandHandlerStub
    //      */
    //     $action = new CreateComment($createCommentCommandHandlerStub);

    //     $response = $action->handle($request);

    //     $this->assertInstanceOf(SuccessfulResponse::class, $response);
    //     $this->expectOutputString(
    //         sprintf(
    //             '{"success":true,"data":{"authorId":"%d","articleId":"%d"}}',
    //             $authorId,
    //             $articleId,
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

        $createCommentCommandHandlerStub = $this->createStub(CreateCommentCommandHandler::class);

        /**
         * @var CreateCommentCommandHandler $createCommentCommandHandlerStub
         */
        $action = new CreateComment($createCommentCommandHandlerStub);

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
        $action = new CreateComment($createCommentCommandHandlerStub);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Empty field: text"}'
        );

        $response->send();
    }
}
