<?php

namespace Tests\Repositories;

use PDOStatement;
use App\Drivers\Connection;
use Tests\Traits\LoggerTrait;
use PHPUnit\Framework\TestCase;
use App\Repositories\UserRepository;
use App\Repositories\ArticleRepository;
use App\Repositories\CommentRepository;
use App\Exceptions\CommentNotFoundException;

class CommentRepositoryTest extends TestCase
{
    use LoggerTrait;

    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionStub = $this->createStub(Connection::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $commentRepository = new CommentRepository(
            $connectionStub,
            $this->createStub(UserRepository::class),
            $this->createStub(ArticleRepository::class),
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

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Comment not found');

        $commentRepository->findById(mt_rand(1, mt_getrandmax()));
    }
}
