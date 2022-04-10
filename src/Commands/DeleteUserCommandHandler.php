<?php

namespace App\Commands;

use App\Drivers\Connection;
use Psr\Log\LoggerInterface;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepositoryInterface;

class DeleteUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private Connection $connection,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param DeleteEntityCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        $this->logger->info("Delete user command started");

        $id = $command->getId();
        if ($this->userRepository->isExists($id)) {
            $this->connection->prepare($this->getSQL())->execute(
                [
                    ':id' => (string)$id
                ]
            );
            $this->logger->info("User deleted id: $id");
        } else {
            $this->logger->warning("User not found: $id");
            throw new UserNotFoundException('User not found');
        }
    }

    public function getSQL(): string
    {
        return "DELETE FROM users WHERE id = :id";
    }
}
