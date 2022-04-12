<?php

namespace Tests\Repositories;

use PDOStatement;
use App\Drivers\Connection;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Repositories\UserRepository;
use App\Repositories\ArticleRepository;
use App\Exceptions\ArticleNotFoundException;

class ArticleRepositoryTest extends TestCase
{
    use LoggerTrait;

    public function testItThrowsAnExceptionWhenArticleNotFound(): void
    {
        $connectionStub = $this->createStub(Connection::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $articleRepository = new ArticleRepository(
            $connectionStub,
            $this->createStub(UserRepository::class),
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

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage('Article not found');

        $articleRepository->findById(mt_rand(1, mt_getrandmax()));
    }
}
