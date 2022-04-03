<?php

namespace App\Commands;

use App\Drivers\Connection;
use App\Entities\Comment\Comment;
use App\Repositories\CommentRepositoryInterface;

class CreateCommentCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $stmt;

    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private Connection $connection
    ) {
        $this->stmt = $connection->prepare($this->getSQL());
    }

    /**
     * @param CreateEntityCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        /**
         * @var Comment $comment
         */
        $comment = $command->getEntity();
        $this->stmt->execute(
            [
                ':author_id' => $comment->getAuthor()->getId(),
                ':article_id' => $comment->getArticle()->getId(),
                ':text' => $comment->getText(),
            ]
        );
    }

    public function getSQL(): string
    {
        return "INSERT INTO comments (author_id, article_id, text) 
        VALUES (:author_id, :article_id, :text)";
    }
}
