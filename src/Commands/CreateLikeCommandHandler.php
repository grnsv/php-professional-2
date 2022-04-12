<?php

namespace App\Commands;

use App\Drivers\Connection;
use App\Entities\Like\Like;
use Psr\Log\LoggerInterface;
use App\Entities\Like\LikeInterface;
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
     * @param EntityCommand $command
     */
    public function handle(CommandInterface $command): LikeInterface
    {
        $this->logger->info("Create like command started");

        /**
         * @var Like $like
         */
        $like = $command->getEntity();

        try {
            $this->connection->beginTransaction();
            $this->connection->prepare($this->getSQL())->execute(
                [
                    ':user_id' => $like->getUser()->getId(),
                    ':article_id' => $like->getArticle()->getId(),
                ]
            );

            $this->connection->commit();
        } catch (\PDOException $e) {
            $this->connection->rollback();
            print "Error!: " . $e->getMessage() . PHP_EOL;
        }

        $data = [
            'id' => $like->getId(),
            'userId' => $like->getUser()->getId(),
            'articleId' => $like->getArticle()->getId(),
        ];

        $this->logger->info('Created new Like', $data);

        return $like->getId() ? $like : $this->likeRepository->findById($this->connection->lastInsertId());
    }

    public function getSQL(): string
    {
        return "INSERT INTO likes (user_id, article_id) 
        VALUES (:user_id, :article_id)";
    }
}
