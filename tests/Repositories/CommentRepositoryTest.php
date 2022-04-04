<?php

namespace Tests\Repositories;

use PDOStatement;
use App\Drivers\Connection;
use PHPUnit\Framework\TestCase;
use App\Repositories\CommentRepository;
use App\Exceptions\CommentNotFoundException;

class CommentRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
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
        $repository = new CommentRepository($connectionStub);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Comment not found');

        $repository->get(mt_rand(1, mt_getrandmax()));
    }
}
