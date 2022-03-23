<?php

namespace App\Migrations;

use App\Connections\ConnectorInterface;
use App\Connections\SqliteConnector;
use JetBrains\PhpStorm\Pure;

class Migration_version_2 implements Migrations
{
    private ConnectorInterface $connector;

    #[Pure] public function __construct(ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new SqliteConnector();
    }

    public function execute(): void
    {
        $this->connector->getConnection()->query("
        CREATE TABLE articles (
            id INTEGER NOT NULL,
            author_id INTEGER NOT NULL,
            title VARCHAR(255) NOT NULL,
            'text' TEXT NOT NULL,
            CONSTRAINT articles_PK PRIMARY KEY (id),
            CONSTRAINT articles_FK FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
        );
        ");
    }
}
