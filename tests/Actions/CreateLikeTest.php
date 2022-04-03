<?php

namespace Tests;

use App\Http\Request;
use App\Http\ErrorResponse;
use PHPUnit\Framework\TestCase;
use App\Http\Actions\CreateLike;
use App\Http\SuccessfulResponse;
use App\Commands\CreateLikeCommandHandler;

class CreateLikeTest extends TestCase
{
    public function argumentsProvider(): iterable
    {
        $userId = mt_rand(1, mt_getrandmax());
        $articleId = mt_rand(1, mt_getrandmax());
        return
            [
                [$userId, $articleId],
            ];
    }

    // /**
    //  * @runInSeparateProcess
    //  * @preserveGlobalState disabled
    //  * @dataProvider argumentsProvider
    //  */
    // public function testItReturnsSuccessfulResponse($userId, $articleId): void
    // {
    //     $request = new Request(
    //         [],
    //         [],
    //         sprintf(
    //             '{"userId":"%d","articleId":"%d"}',
    //             $userId,
    //             $articleId,
    //         )
    //     );

    //     $createLikeCommandHandlerStub = $this->createStub(CreateLikeCommandHandler::class);

    //     /**
    //      * @var CreateLikeCommandHandler $createLikeCommandHandlerStub
    //      */
    //     $action = new CreateLike($createLikeCommandHandlerStub);

    //     $response = $action->handle($request);

    //     $this->assertInstanceOf(SuccessfulResponse::class, $response);
    //     $this->expectOutputString(
    //         sprintf(
    //             '{"success":true,"data":{"userId":"%d","articleId":"%d"}}',
    //             $userId,
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

        $createLikeCommandHandlerStub = $this->createStub(CreateLikeCommandHandler::class);

        /**
         * @var CreateLikeCommandHandler $createLikeCommandHandlerStub
         */
        $action = new CreateLike($createLikeCommandHandlerStub);

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
        $action = new CreateLike($createLikeCommandHandlerStub);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Empty field: articleId"}'
        );

        $response->send();
    }
}
