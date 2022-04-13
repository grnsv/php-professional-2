<?php

namespace Tests\Actions;

use Faker\Factory;
use Faker\Generator;
use App\Http\Request;
use App\Entities\User\User;
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
    public function testItReturnsSuccessfulResponse($user, $password): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"email":"%s","firstName":"%s","lastName":"%s", "password":"%s"}',
                $user->getEmail(),
                $user->getFirstName(),
                $user->getLastName(),
                $password,
            )
        );

        $createUserCommandHandler = $this->createStub(CreateUserCommandHandler::class);

        $action = new CreateUser(
            $createUserCommandHandler,
            $this->getLogger(),
        );

        /**
         * @var Stub $createUserCommandHandler
         */
        $createUserCommandHandler->method('handle')->willReturn($user);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString(
            sprintf(
                '{"success":true,"data":{"id":%d,"firstName":"%s","lastName":"%s","email":"%s"}}',
                $user->getId(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmail(),
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

        $action = new CreateUser(
            $this->createStub(CreateUserCommandHandler::class),
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
    public function testItReturnsErrorResponseIfNoEmailProvided($user, $password): void
    {
        $request = new Request(
            [],
            [],
            sprintf(
                '{"email":"","firstName":"%s","lastName":"%s", "password":"%s"}',
                $user->getFirstName(),
                $user->getLastName(),
                $password,
            )
        );

        $action = new CreateUser(
            $this->createStub(CreateUserCommandHandler::class),
            $this->getLogger(),
        );

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"Empty field: email"}'
        );

        $response->send();
    }

    private function getTestData(): array
    {
        $password = $this->faker->password();

        $user = new User(
            $this->faker->firstName(),
            $this->faker->lastName(),
            $this->faker->email(),
            $password,
        );
        $user->setId(mt_rand(1, mt_getrandmax()));
        $user->setPassword($password);

        return [$user, $password];
    }
}
