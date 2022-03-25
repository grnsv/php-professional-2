<?php

namespace Tests\Repositories;

use PDOStatement;
use App\Drivers\Connection;
use PHPUnit\Framework\TestCase;
use App\Connections\ConnectorInterface;
use App\Repositories\ArticleRepository;
use App\Exceptions\ArticleNotFoundException;

class ArticleRepositoryTest extends TestCase
{
    public function __construct(
        ?string $name = null,
        array $data = [],
        $dataName = '',
    ) {
        parent::__construct($name, $data, $dataName);
    }

    public function testItThrowsAnExceptionWhenArticleNotFound(): void
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
        $repository = new ArticleRepository($connectorStub);

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage('Article not found');

        $repository->get(mt_rand(1, mt_getrandmax()));
    }
}
