<?php

namespace App\Commands;

use App\Drivers\Connection;
use App\Exceptions\LikeNotFoundException;
use App\Repositories\LikeRepositoryInterface;

class DeleteLikeCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $stmt;

    public function __construct(
        private LikeRepositoryInterface $likeRepository,
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
        if ($this->likeRepository->isExists($id)) {
            $this->stmt->execute(
                [
                    ':id' => (string)$id
                ]
            );
        } else {
            throw new LikeNotFoundException('Like not found');
        }
    }


    public function getSQL(): string
    {
        return "DELETE FROM likes WHERE id = :id";
    }
}
