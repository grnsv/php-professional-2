<?php

namespace Tests\Repositories;

use PDOStatement;
use App\Drivers\Connection;
use PHPUnit\Framework\TestCase;
use App\Connections\ConnectorInterface;
use App\Repositories\CommentRepository;
use App\Exceptions\CommentNotFoundException;

class CommentRepositoryTest extends TestCase
{
    public function __construct(
        ?string $name = null,
        array $data = [],
        $dataName = '',
    ) {
        parent::__construct($name, $data, $dataName);
    }

    public function testItThrowsAnExceptionWhenCommentNotFound(): void
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
        $repository = new CommentRepository($connectorStub);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Comment not found');

        $repository->get(mt_rand(1, mt_getrandmax()));
    }
}
