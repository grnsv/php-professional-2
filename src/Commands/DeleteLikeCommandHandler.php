<?php

namespace App\Commands;

use App\Drivers\Connection;
use App\Entities\Like\Like;
use Psr\Log\LoggerInterface;
use App\Exceptions\LikeNotFoundException;
use App\Repositories\LikeRepositoryInterface;

class DeleteLikeCommandHandler implements CommandHandlerInterface
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
    public function handle(CommandInterface $command): void
    {
        $this->logger->info("Delete like command started");

        /**
         * @var Like $like
         */
        $like = $command->getEntity();
        $id = $like->getId();

        if ($this->likeRepository->isExists($id)) {
            $this->connection->prepare($this->getSQL())->execute(
                [
                    ':id' => (string)$id
                ]
            );
            $this->logger->info("Like deleted id: $id");
        } else {
            $this->logger->warning("Like not found: $id");
            throw new LikeNotFoundException('Like not found');
        }
    }


    public function getSQL(): string
    {
        return "DELETE FROM likes WHERE id = :id";
    }
}
