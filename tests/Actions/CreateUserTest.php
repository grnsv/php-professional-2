<?php

namespace Tests\Actions;

use Faker\Factory;
use Faker\Generator;
use App\Http\Request;
use App\Http\ErrorResponse;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Http\Actions\CreateUser;
use App\Http\SuccessfulResponse;
use App\Commands\CreateUserCommandHandler;

class CreateUserTest extends TestCase
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
                [
                    $this->faker->userName(),
                    $this->faker->word(),
                    $this->faker->email(),
                    $this->faker->word(), //password() - doesn't work with json
                ],
            ];
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider argumentsProvider
     */
    public function testItReturnsSuccessfulResponse($firstName, $lastName, $email, $password): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"email":"%s","firstName":"%s","lastName":"%s", "password":"%s"}',
                $email,
                $firstName,
                $lastName,
                $password,
            )
        );

        $createUserCommandHandlerStub = $this->createStub(CreateUserCommandHandler::class);

        /**
         * @var CreateUserCommandHandler $createUserCommandHandlerStub
         */
        $action = new CreateUser($createUserCommandHandlerStub, $this->getLogger());

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString(
            sprintf(
                '{"success":true,"data":{"firstName":"%s","lastName":"%s","email":"%s"}}',
                $firstName,
                $lastName,
                $email,
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

        $createUserCommandHandlerStub = $this->createStub(CreateUserCommandHandler::class);

        /**
         * @var CreateUserCommandHandler $createUserCommandHandlerStub
         */
        $action = new CreateUser($createUserCommandHandlerStub, $this->getLogger());

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
    public function testItReturnsErrorResponseIfNoEmailProvided($firstName, $lastName): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"email":"","firstName":"%s","lastName":"%s"}',
                $firstName,
                $lastName,
            )
        );

        $createUserCommandHandlerStub = $this->createStub(CreateUserCommandHandler::class);

        /**
         * @var CreateUserCommandHandler $createUserCommandHandlerStub
         */
        $action = new CreateUser($createUserCommandHandlerStub, $this->getLogger());

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Empty field: email"}'
        );

        $response->send();
    }
}
