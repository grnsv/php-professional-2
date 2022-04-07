<?php

namespace App\Commands;

use App\Drivers\Connection;
use App\Entities\Like\Like;
use Psr\Log\LoggerInterface;
use App\Repositories\LikeRepositoryInterface;

class CreateLikeCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private LikeRepositoryInterface $likeRepository,
        private Connection $connection,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param CreateEntityCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        $this->logger->info("Create like command started");

        /**
         * @var Like $like
         */
        $like = $command->getEntity();
        $userId = $like->getUser()->getId();
        $articleId = $like->getArticle()->getId();

        $result = $this->connection->prepare($this->getSQL())->execute(
            [
                ':user_id' => $userId,
                ':article_id' => $articleId,
            ]
        );
        if ($result) {
            $this->logger->info("Like created userId: $userId articleId: $articleId");
        }
    }

    public function getSQL(): string
    {
        return "INSERT INTO likes (user_id, article_id) 
        VALUES (:user_id, :article_id)";
    }
}
