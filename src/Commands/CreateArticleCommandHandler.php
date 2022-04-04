<?php

namespace App\Commands;

use App\Drivers\Connection;
use App\Entities\Article\Article;
use App\Repositories\ArticleRepositoryInterface;

class CreateArticleCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $stmt;

    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
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
         * @var Article $article
         */
        $article = $command->getEntity();
        $this->stmt->execute(
            [
                ':author_id' => $article->getAuthor()->getId(),
                ':title' => $article->getTitle(),
                ':text' => $article->getText(),
            ]
        );
    }

    public function getSQL(): string
    {
        return "INSERT INTO articles (author_id, title, text) 
        VALUES (:author_id, :title, :text)";
    }
}
