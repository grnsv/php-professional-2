<?php

namespace App\Commands;

use App\Drivers\Connection;
use Psr\Log\LoggerInterface;
use App\Entities\Comment\Comment;
use App\Exceptions\CommentNotFoundException;
use App\Repositories\CommentRepositoryInterface;

class DeleteCommentCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private Connection $connection,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param EntityCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        $this->logger->info("Delete comment command started");

        /**
         * @var Comment $comment
         */
        $comment = $command->getEntity();
        $id = $comment->getId();

        if ($this->commentRepository->isExists($id)) {
            $this->connection->prepare($this->getSQL())->execute(
                [
                    ':id' => (string)$id
                ]
            );
            $this->logger->info("Comment deleted id: $id");
        } else {
            $this->logger->warning("Comment not found: $id");
            throw new CommentNotFoundException('Comment not found');
        }
    }


    public function getSQL(): string
    {
        return "DELETE FROM comments WHERE id = :id";
    }
}
