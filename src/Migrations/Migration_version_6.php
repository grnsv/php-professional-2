<?php

namespace App\Migrations;

use App\Connections\ConnectorInterface;
use App\Connections\SqliteConnector;
use JetBrains\PhpStorm\Pure;

class Migration_version_6 implements Migrations
{
    private ConnectorInterface $connector;

    #[Pure] public function __construct(ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new SqliteConnector();
    }

    public function execute(): void
    {
        $this->connector->getConnection()->query("
        CREATE TABLE tokens (
            token TEXT NOT NULL,
            user_id INTEGER NOT NULL,
            expires_on TEXT NOT NULL,
            CONSTRAINT tokens_PK PRIMARY KEY (token),
            CONSTRAINT likes_FK FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
        );
        ");
    }
}
