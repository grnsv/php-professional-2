<?php

namespace App\Commands;

use App\Drivers\Connection;
use App\Entities\Like\Like;
use App\Repositories\LikeRepositoryInterface;

class CreateLikeCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $stmt;

    public function __construct(
        private LikeRepositoryInterface $likeRepository,
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
         * @var Like $like
         */
        $like = $command->getEntity();
        $this->stmt->execute(
            [
                ':author_id' => $like->getUser()->getId(),
                ':article_id' => $like->getArticle()->getId(),
            ]
        );
    }

    public function getSQL(): string
    {
        return "INSERT INTO likes (author_id, article_id) 
        VALUES (:author_id, :article_id)";
    }
}
