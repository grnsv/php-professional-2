<?php

namespace App\Commands;

use App\Drivers\Connection;
use Psr\Log\LoggerInterface;
use App\Entities\Article\Article;
use App\Entities\Article\ArticleInterface;
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
     * @param EntityCommand $command
     */
    public function handle(CommandInterface $command): ArticleInterface
    {
        $this->logger->info("Create article command started");

        /**
         * @var Article $article
         */
        $article = $command->getEntity();

        try {
            $this->connection->beginTransaction();
            $this->connection->prepare($this->getSQL())->execute(
                [
                    ':author_id' => $article->getAuthor()->getId(),
                    ':title' => $article->getTitle(),
                    ':text' => $article->getText(),
                ]
            );

            $this->connection->commit();
        } catch (\PDOException $e) {
            $this->connection->rollback();
            print "Error!: " . $e->getMessage() . PHP_EOL;
        }

        $data = [
            'id' => $article->getId(),
            'authorId' => $article->getAuthor()->getId(),
            'title' => $article->getTitle(),
            'text' => $article->getText(),
        ];

        $this->logger->info('Created new Article', $data);

        return $article->getId() ? $article : $this->articleRepository->findById($this->connection->lastInsertId());
    }

    public function getSQL(): string
    {
        return "INSERT INTO articles (author_id, title, text) 
        VALUES (:author_id, :title, :text)";
    }
}
