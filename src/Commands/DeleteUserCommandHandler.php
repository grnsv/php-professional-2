<?php

namespace App\Commands;

use App\Connections\SqliteConnector;
use App\Connections\ConnectorInterface;

class DeleteUserCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $stmt;

    public function __construct(private ?ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new SqliteConnector();
        $this->stmt = $this->connector->getConnection()->prepare($this->getSQL());
    }

    /**
     * @param DeleteEntityCommand $command
     */
    public function handle(CommandInterface $command): void
    {
        $id = $command->getId();
        $this->stmt->execute(
            [
                ':id' => (string)$id
            ]
        );
    }

    public function getSQL(): string
    {
        return "DELETE FROM users WHERE id = :id";
    }
}
