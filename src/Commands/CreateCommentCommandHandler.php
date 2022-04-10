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
     * @param CreateEntityCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        $this->logger->info("Create comment command started");

        /**
         * @var Comment $comment
         */
        $comment = $command->getEntity();
        $authorId = $comment->getAuthor()->getId();
        $articleId = $comment->getArticle()->getId();

        try {
            $this->connection->beginTransaction();
            $this->connection->prepare($this->getSQL())->execute(
                [
                    ':author_id' => $authorId,
                    ':article_id' => $articleId,
                    ':text' => $comment->getText(),
                ]
            );

            $this->connection->commit();
        } catch (\PDOException $e) {
            $this->connection->rollback();
            print "Error!: " . $e->getMessage() . PHP_EOL;
        }
        $this->logger->info("Comment created authorId: $authorId articleId: $articleId");
    }

    public function getSQL(): string
    {
        return "INSERT INTO comments (author_id, article_id, text) 
        VALUES (:author_id, :article_id, :text)";
    }
}
