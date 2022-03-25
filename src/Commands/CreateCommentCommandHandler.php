<?php

namespace App\Commands;

use App\Entities\Comment\Comment;
use App\Connections\SqliteConnector;
use App\Connections\ConnectorInterface;

class CreateCommentCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $stmt;

    public function __construct(private ?ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new SqliteConnector();
        $this->stmt = $this->connector->getConnection()->prepare($this->getSQL());
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
