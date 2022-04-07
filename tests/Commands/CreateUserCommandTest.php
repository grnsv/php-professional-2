<?php

namespace Tests\Commands;

use PDOStatement;
use Faker\Factory;
use Faker\Generator;
use App\Drivers\Connection;
use App\Entities\User\User;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Repositories\UserRepository;
use App\Commands\CreateEntityCommand;
use App\Exceptions\UserNotFoundException;
use App\Commands\CreateUserCommandHandler;
use App\Exceptions\UserEmailExistsException;
use App\Repositories\UserRepositoryInterface;

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
                [$this->faker->userName(), $this->faker->word(), $this->faker->email()],
            ];
    }

    /**
     * @dataProvider argumentsProvider
     */
    public function testItThrowsAnExceptionWhenUserAlreadyExists($firstName, $lastName, $email): void
    {
        /**
         * @var Stub $connectionStub
         */
        $connectionStub = $this->createStub(Connection::class);
        $connectionStub->method('prepare')->willReturn(
            $this->createStub(PDOStatement::class)
        );
        /**
         * @var Stub $userRepositoryStub
         */
        $userRepositoryStub = $this->createStub(UserRepository::class);
        $userRepositoryStub->method('isUserExists')->willReturn(true);

        /**
         * @var UserRepository $userRepositoryStub
         * @var Connection $connectionStub
         */
        $createUserCommandHandler = new CreateUserCommandHandler(
            $userRepositoryStub,
            $connectionStub,
            $this->getLogger(),
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
        /**
         * @var Stub $connectionStub
         */
        $connectionStub = $this->createStub(Connection::class);
        /**
         * @var Stub $userRepositoryStub
         */
        $userRepositoryStub = $this->createStub(UserRepository::class);
        /**
         * @var MockObject $statementMock
         */
        $statementMock = $this->createMock(PDOStatement::class);

        $userRepositoryStub->method('isUserExists')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementMock);
        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':firstName' => $firstName,
                ':lastName' => $lastName,
                ':email' => $email,
            ]);

        /**
         * @var UserRepositoryInterface $userRepositoryStub
         * @var Connection $connectionStub
         */
        $createUserCommandHandler = new CreateUserCommandHandler(
            $userRepositoryStub,
            $connectionStub,
            $this->getLogger(),
        );

        $command = new CreateEntityCommand(
            new User(
                $firstName,
                $lastName,
                $email
            )
        );

        $createUserCommandHandler->handle($command);
    }
}
