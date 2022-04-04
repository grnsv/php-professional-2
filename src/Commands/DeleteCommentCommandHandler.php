<?php

namespace App\Commands;

use App\Drivers\Connection;
use App\Exceptions\CommentNotFoundException;
use App\Repositories\CommentRepositoryInterface;

class DeleteCommentCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $stmt;

    public function __construct(
        private CommentRepositoryInterface $commentRepository,
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
        if ($this->commentRepository->isExists($id)) {
            $this->stmt->execute(
                [
                    ':id' => (string)$id
                ]
            );
        } else {
            throw new CommentNotFoundException('Comment not found');
        }
    }


    public function getSQL(): string
    {
        return "DELETE FROM comments WHERE id = :id";
    }
}
