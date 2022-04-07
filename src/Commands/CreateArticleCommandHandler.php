<?php

namespace App\Commands;

use App\Drivers\Connection;
use Psr\Log\LoggerInterface;
use App\Entities\Article\Article;
use App\Repositories\ArticleRepositoryInterface;

class CreateArticleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private Connection $connection,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param CreateEntityCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        $this->logger->info("Create article command started");

        /**
         * @var Article $article
         */
        $article = $command->getEntity();
        $authorId = $article->getAuthor()->getId();
        $title = $article->getTitle();

        $result = $this->connection->prepare($this->getSQL())->execute(
            [
                ':author_id' => $authorId,
                ':title' => $title,
                ':text' => $article->getText(),
            ]
        );
        if ($result) {
            $this->logger->info("Article created authorId: $authorId title: $title");
        }
    }

    public function getSQL(): string
    {
        return "INSERT INTO articles (author_id, title, text) 
        VALUES (:author_id, :title, :text)";
    }
}
