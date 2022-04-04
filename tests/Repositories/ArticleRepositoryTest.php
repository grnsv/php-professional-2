<?php

namespace Tests\Repositories;

use PDOStatement;
use App\Drivers\Connection;
use PHPUnit\Framework\TestCase;
use App\Repositories\ArticleRepository;
use App\Exceptions\ArticleNotFoundException;

class ArticleRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenArticleNotFound(): void
    {
        /**
         * @var Stub $connectionStub
         */
        $connectionStub = $this->createStub(Connection::class);
        /**
         * @var Stub $statementStub
         */
        $statementStub = $this->createStub(PDOStatement::class);

        $connectionStub->method('prepare')->willReturn($statementStub);
        $statementStub->method('fetch')->willReturn(false);

        /**
         * @var Connection $connectionStub
         */
        $repository = new ArticleRepository($connectionStub);

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage('Article not found');

        $repository->get(mt_rand(1, mt_getrandmax()));
    }
}
