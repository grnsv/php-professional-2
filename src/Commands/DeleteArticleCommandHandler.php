<?php

namespace App\Commands;

use App\Drivers\Connection;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\ArticleRepositoryInterface;

class DeleteArticleCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $stmt;

    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private Connection $connection
    ) {
        $this->stmt = $connection->prepare($this->getSQL());
    }

    /**
     * @param DeleteEntityCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        $id = $command->getId();
        if ($this->articleRepository->isExists($id)) {
            $this->stmt->execute(
                [
                    ':id' => (string)$id
                ]
            );
        } else {
            throw new ArticleNotFoundException('Article not found');
        }
    }


    public function getSQL(): string
    {
        return "DELETE FROM articles WHERE id = :id";
    }
}
