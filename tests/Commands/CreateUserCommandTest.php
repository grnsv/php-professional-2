<?php

namespace Tests\Commands;

use Faker\Factory;
use Faker\Generator;
use App\Entities\User\User;
use PHPUnit\Framework\TestCase;
use App\Entities\EntityInterface;
use App\Drivers\PdoConnectionDriver;
use App\Repositories\UserRepository;
use App\Commands\CreateEntityCommand;
use App\Connections\ConnectorInterface;
use App\Exceptions\UserNotFoundException;
use App\Commands\CreateUserCommandHandler;
use App\Exceptions\UserEmailExistsException;
use App\Repositories\UserRepositoryInterface;

class CreateUserCommandTest extends TestCase
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
                [$this->faker->userName(), $this->faker->word(), $this->faker->email()],
            ];
    }

    /**
     * @dataProvider argumentsProvider
     */
    public function testItThrowsAnExceptionWhenUserAlreadyExists($firstName, $lastName, $email): void
    {
        /**
         * @var Stub $connectorStub
         */
        $connectorStub = $this->createStub(ConnectorInterface::class);

        /**
         * @var Stub $userRepositoryStub
         */
        $userRepositoryStub = $this->createStub(UserRepository::class);

        $connectorStub->method('getConnection')->willReturn(
            $this->createStub(PdoConnectionDriver::class)
        );
        $userRepositoryStub->method('getUserByEmail')->willReturn(
            new User($firstName, $lastName, $email)
        );

        /**
         * @var ConnectorInterface $connectorStub
         * @var UserRepository $userRepositoryStub
         */
        $createUserCommandHandler = new CreateUserCommandHandler(
            $userRepositoryStub,
            $connectorStub,
        );

        $this->expectException(UserEmailExistsException::class);
        $this->expectExceptionMessage('Пользователь с таким email уже существует в системе');

        $command = new CreateEntityCommand(
            new User(
                $firstName,
                $lastName,
                $email
            )
        );

        $createUserCommandHandler->handle($command);
    }

    /**
     * @throws UserNotFoundException
     * @dataProvider argumentsProvider
     */
    public function testItSavesUserToDatabase($firstName, $lastName, $email): void
    {
        $userRepository = new class($firstName, $lastName, $email) implements UserRepositoryInterface
        {
            private bool $called = false;

            public function __construct(
                private string $firstName,
                private string $lastName,
                private string $email
            ) {
            }

            public function get(int $id): EntityInterface
            {
                throw new UserNotFoundException("Not found");
            }

            public function getUserByEmail(string $email): User
            {
                $this->called = true;
                return new User(
                    $this->firstName,
                    $this->lastName,
                    $this->email
                );
            }

            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        $createUserCommandHandler = new CreateUserCommandHandler($userRepository);

        $this->expectException(UserEmailExistsException::class);
        $this->expectExceptionMessage('Пользователь с таким email уже существует в системе');

        $command = new CreateEntityCommand(
            new User(
                $firstName,
                $lastName,
                $email
            )
        );

        $createUserCommandHandler->handle($command);
        $this->assertTrue($userRepository->wasCalled());
    }
}
