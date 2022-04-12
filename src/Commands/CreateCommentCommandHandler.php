<?php

namespace App\Commands;

use App\Drivers\Connection;
use Psr\Log\LoggerInterface;
use App\Entities\Comment\Comment;
use App\Entities\Comment\CommentInterface;
use App\Repositories\CommentRepositoryInterface;

class CreateCommentCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private Connection $connection,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param EntityCommand $command
     */
    public function handle(CommandInterface $command): CommentInterface
    {
        $this->logger->info("Create comment command started");

        /**
         * @var Comment $comment
         */
        $comment = $command->getEntity();

        try {
            $this->connection->beginTransaction();
            $this->connection->prepare($this->getSQL())->execute(
                [
                    ':author_id' => $comment->getAuthor()->getId(),
                    ':article_id' => $comment->getArticle()->getId(),
                    ':text' => $comment->getText(),
                ]
            );

            $this->connection->commit();
        } catch (\PDOException $e) {
            $this->connection->rollback();
            print "Error!: " . $e->getMessage() . PHP_EOL;
        }

        $data = [
            'id' => $comment->getId(),
            'authorId' => $comment->getAuthor()->getId(),
            'articleId' => $comment->getArticle()->getId(),
            'text' => $comment->getText(),
        ];

        $this->logger->info('Created new Comment', $data);

        return $comment->getId() ? $comment : $this->commentRepository->findById($this->connection->lastInsertId());
    }

    public function getSQL(): string
    {
        return "INSERT INTO comments (author_id, article_id, text) 
        VALUES (:author_id, :article_id, :text)";
    }
}
