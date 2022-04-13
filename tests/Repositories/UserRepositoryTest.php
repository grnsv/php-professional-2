<?php

namespace Tests\Repositories;

use PDOStatement;
use Faker\Factory;
use Faker\Generator;
use App\Drivers\Connection;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Repositories\UserRepository;
use App\Exceptions\UserNotFoundException;

class UserRepositoryTest extends TestCase
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

    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionStub = $this->createStub(Connection::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $userRepository = new UserRepository(
            $connectionStub,
            $this->getLogger(),
        );

        /**
         * @var Stub $connectionStub
         */
        $connectionStub->method('prepare')->willReturn($statementStub);

        /**
         * @var Stub $statementStub
         */
        $statementStub->method('fetch')->willReturn(false);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $userRepository->findById(mt_rand(1, mt_getrandmax()));
    }

    public function testItThrowsAnExceptionWhenUserNotFoundByEmail(): void
    {
        $connectionStub = $this->createStub(Connection::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $userRepository = new UserRepository(
            $connectionStub,
            $this->getLogger(),
        );

        /**
         * @var Stub $connectionStub
         */
        $connectionStub->method('prepare')->willReturn($statementStub);

        /**
         * @var Stub $statementStub
         */
        $statementStub->method('fetch')->willReturn(false);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $userRepository->getUserByEmail($this->faker->email());
    }
}
