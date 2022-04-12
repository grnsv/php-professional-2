<?php

namespace Tests\Commands;

use PDOStatement;
use Faker\Factory;
use Faker\Generator;
use App\Drivers\Connection;
use App\Entities\User\User;
use Tests\Traits\LoggerTrait;
use App\Commands\EntityCommand;
use PHPUnit\Framework\TestCase;
use App\Repositories\UserRepository;
use App\Exceptions\UserNotFoundException;
use App\Commands\CreateUserCommandHandler;

class CreateUserCommandTest extends TestCase
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
     * @dataProvider argumentsProvider
     */
    public function testItThrowsAnExceptionWhenUserAlreadyExists($user, $password): void
    {
        $userRepositoryStub = $this->createStub(UserRepository::class);
        $connectionStub = $this->createStub(Connection::class);

        $createUserCommandHandler = new CreateUserCommandHandler(
            $userRepositoryStub,
            $connectionStub,
            $this->getLogger(),
        );

        /**
         * @var Stub $connectionStub
         */
        $connectionStub->method('prepare')->willReturn(
            $this->createStub(PDOStatement::class)
        );

        /**
         * @var Stub $userRepositoryStub
         */
        $userRepositoryStub->method('findById')->willThrowException(
            new UserNotFoundException('User not found')
        );

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $command = new EntityCommand(
            new User(
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmail(),
                $password,
            )
        );

        $createUserCommandHandler->handle($command);
    }

    /**
     * @throws UserNotFoundException
     * @dataProvider argumentsProvider
     */
    public function testItSavesUserToDatabase($user, $password): void
    {
        $userRepositoryStub = $this->createStub(UserRepository::class);
        $connectionStub = $this->createStub(Connection::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $createUserCommandHandler = new CreateUserCommandHandler(
            $userRepositoryStub,
            $connectionStub,
            $this->getLogger(),
        );

        /**
         * @var Stub $connectionStub
         */
        $connectionStub->method('prepare')->willReturn($statementMock);

        /**
         * @var MockObject $statementMock
         */
        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':firstName' => $user->getFirstName(),
                ':lastName' => $user->getLastName(),
                ':email' => $user->getEmail(),
                ':password' => $user->getPassword(),
            ]);

        $command = new EntityCommand(
            new User(
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmail(),
                $password,
            )
        );

        $createUserCommandHandler->handle($command);
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
