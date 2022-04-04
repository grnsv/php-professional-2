<?php

namespace App\Commands;

use App\Drivers\Connection;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepositoryInterface;

class DeleteUserCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $stmt;

    public function __construct(
        private UserRepositoryInterface $userRepository,
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
        if ($this->userRepository->isExists($id)) {
            $this->stmt->execute(
                [
                    ':id' => (string)$id
                ]
            );
        } else {
            throw new UserNotFoundException('User not found');
        }
    }

    public function getSQL(): string
    {
        return "DELETE FROM users WHERE id = :id";
    }
}
