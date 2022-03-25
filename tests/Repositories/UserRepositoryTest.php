<?php

namespace Tests\Repositories;

use PDOStatement;
use Faker\Factory;
use Faker\Generator;
use App\Drivers\Connection;
use PHPUnit\Framework\TestCase;
use App\Repositories\UserRepository;
use App\Connections\ConnectorInterface;
use App\Exceptions\UserNotFoundException;

class UserRepositoryTest extends TestCase
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

    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        /**
         * @var Stub $connectorStub
         */
        $connectorStub = $this->createStub(ConnectorInterface::class);
        /**
         * @var Stub $connectionStub
         */
        $connectionStub = $this->createStub(Connection::class);
        /**
         * @var Stub $statementStub
         */
        $statementStub = $this->createStub(PDOStatement::class);

        $connectorStub->method('getConnection')->willReturn($connectionStub);
        $connectionStub->method('prepare')->willReturn($statementStub);
        $statementStub->method('fetch')->willReturn(false);

        /**
         * @var ConnectorInterface $connectorStub
         */
        $repository = new UserRepository($connectorStub);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $repository->get(mt_rand(1, mt_getrandmax()));
    }

    public function testItThrowsAnExceptionWhenUserNotFoundByEmail(): void
    {
        /**
         * @var Stub $connectorStub
         */
        $connectorStub = $this->createStub(ConnectorInterface::class);
        /**
         * @var Stub $connectionStub
         */
        $connectionStub = $this->createStub(Connection::class);
        /**
         * @var Stub $statementStub
         */
        $statementStub = $this->createStub(PDOStatement::class);

        $connectorStub->method('getConnection')->willReturn($connectionStub);
        $connectionStub->method('prepare')->willReturn($statementStub);
        $statementStub->method('fetch')->willReturn(false);

        /**
         * @var ConnectorInterface $connectorStub
         */
        $repository = new UserRepository($connectorStub);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $repository->getUserByEmail($this->faker->email());
    }
}
