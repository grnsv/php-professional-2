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
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepositoryInterface;

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
                [$this->faker->email(), $this->faker->userName(), $this->faker->word()],
            ];
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfNoEmailProvided(): void
    {
        $request = new Request([], [], '');
        $userRepository = $this->getUserRepository([]);

        $action = new FindByEmail($userRepository, $this->getLogger());
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
    public function testItReturnsErrorResponseIfUserNotFound($email): void
    {
        $request = new Request(['email' => $email], [], '');

        $userRepository = $this->getUserRepository([]);
        $action = new FindByEmail($userRepository, $this->getLogger());

        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Not found"}');
        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider argumentsProvider
     */
    public function testItReturnsSuccessfulResponse($email, $firstName, $lastName): void
    {
        $request = new Request(['email' => $email], [], '');

        $userRepository = $this->getUserRepository([
            new User(
                $firstName,
                $lastName,
                $email
            ),
        ]);

        $action = new FindByEmail($userRepository, $this->getLogger());
        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString(
            sprintf(
                '{"success":true,"data":{"email":"%s","name":"%s %s"}}',
                $email,
                $firstName,
                $lastName,
            )
        );

        $response->send();
    }

    private function getUserRepository(array $users): UserRepositoryInterface
    {
        return new class($users) implements UserRepositoryInterface
        {

            public function __construct(
                private array $users
            ) {
            }

            public function get(int $id): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getUserByEmail(string $email): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $email === $user->getEmail()) {
                        return $user;
                    }
                }

                throw new UserNotFoundException("Not found");
            }

            public function isExists(int $id): bool
            {
                return false;
            }

            public function isUserExists(string $email): bool
            {
                return false;
            }
        };
    }
}
