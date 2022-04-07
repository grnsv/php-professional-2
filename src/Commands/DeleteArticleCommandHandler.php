<?php

namespace App\Commands;

use App\Drivers\Connection;
use Psr\Log\LoggerInterface;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\ArticleRepositoryInterface;

class DeleteArticleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private Connection $connection,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param DeleteEntityCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        $this->logger->info("Delete article command started");

        $id = $command->getId();
        if ($this->articleRepository->isExists($id)) {
            $this->connection->executeQuery(
                $this->getSQL(),
                [
                    ':id' => (string)$id
                ]
            );
            $this->logger->info("Article deleted id: $id");
        } else {
            $this->logger->warning("Article not found: $id");
            throw new ArticleNotFoundException('Article not found');
        }
    }


    public function getSQL(): string
    {
        return "DELETE FROM articles WHERE id = :id";
    }
}
