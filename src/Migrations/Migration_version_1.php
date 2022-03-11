<?php

namespace App\Migrations;

use App\Connections\ConnectorInterface;
use App\Connections\SqliteConnector;
use JetBrains\PhpStorm\Pure;

class Migration_version_1 implements Migrations
{
    private ConnectorInterface $connector;

    #[Pure] public function __construct(ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new SqliteConnector();
    }

    public function execute(): void
    {
        $this->connector->getConnection()->query("
        CREATE TABLE users (
            id INTEGER NOT NULL,
            email VARCHAR(255) NOT NULL,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            CONSTRAINT users_PK PRIMARY KEY (id)
        );
        CREATE UNIQUE INDEX users_email_IDX ON users (email);
        ");
    }
}
