<?php

namespace App\Migrations;

use App\Connections\ConnectorInterface;
use App\Connections\SqliteConnector;
use JetBrains\PhpStorm\Pure;

class Migration_version_3 implements Migrations
{
    private ConnectorInterface $connector;

    #[Pure] public function __construct(ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new SqliteConnector();
    }

    public function execute(): void
    {
        $this->connector->getConnection()->query("
        CREATE TABLE comments (
            id INTEGER NOT NULL,
            article_id INTEGER NOT NULL,
            author_id INTEGER NOT NULL,
            'text' TEXT NOT NULL,
            CONSTRAINT comments_PK PRIMARY KEY (id),
            CONSTRAINT comments_FK FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT comments_FK_1 FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
        );
        ");
    }
}
