<?php

namespace Tests\Repositories;

use PDOStatement;
use App\Drivers\Connection;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Repositories\ArticleRepository;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\UserRepository;

class ArticleRepositoryTest extends TestCase
{
    use LoggerTrait;

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
        $repository = new ArticleRepository(
            $connectionStub,
            $this->createStub(UserRepository::class),
            $this->getLogger(),
        );

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage('Article not found');

        $repository->get(mt_rand(1, mt_getrandmax()));
    }
}
