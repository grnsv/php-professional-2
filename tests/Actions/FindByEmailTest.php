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
use App\Http\Actions\FindByEmail;
use App\Repositories\UserRepository;
use App\Exceptions\UserNotFoundException;

class FindByEmailTest extends TestCase
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
     */
    public function testItReturnsErrorResponseIfNoEmailProvided(): void
    {
        $request = new Request([], [], '');

        $action = new FindByEmail(
            $this->createStub(UserRepository::class),
            $this->getLogger(),
        );

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString(
            '{"success":false,"reason":"No such query param in the request: email"}'
        );

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider argumentsProvider
     */
    public function testItReturnsErrorResponseIfUserNotFound($user): void
    {
        $request = new Request(['email' => $user->getEmail()], [], '');

        $userRepositoryStub = $this->createStub(UserRepository::class);

        $action = new FindByEmail(
            $userRepositoryStub,
            $this->getLogger(),
        );

        /**
         * @var Stub $userRepositoryStub
         */
        $userRepositoryStub->method('getUserByEmail')->willThrowException(
            new UserNotFoundException('User not found')
        );

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"User not found"}');

        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider argumentsProvider
     */
    public function testItReturnsSuccessfulResponse($user, $password): void
    {
        $request = new Request(['email' => $user->getEmail()], [], '');

        $userRepositoryStub = $this->createStub(UserRepository::class);

        $action = new FindByEmail(
            $userRepositoryStub,
            $this->getLogger(),
        );

        /**
         * @var Stub $userRepositoryStub
         */
        $userRepositoryStub->method('getUserByEmail')->willReturn($user);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString(
            sprintf(
                '{"success":true,"data":{"email":"%s","name":"%s %s"}}',
                $user->getEmail(),
                $user->getFirstName(),
                $user->getLastName(),
            )
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

        return [$user, $password];
    }
}
