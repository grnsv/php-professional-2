<?php

namespace App\Migrations;

use App\Connections\ConnectorInterface;
use App\Connections\SqliteConnector;
use JetBrains\PhpStorm\Pure;

class Migration_version_4 implements Migrations
{
    private ConnectorInterface $connector;

    #[Pure] public function __construct(ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new SqliteConnector();
    }

    public function execute(): void
    {
        $this->connector->getConnection()->query("
        CREATE TABLE likes (
            id INTEGER NOT NULL,
            article_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            CONSTRAINT likes_PK PRIMARY KEY (id),
            CONSTRAINT likes_FK FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT likes_FK_1 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
        );
        ");

        $this->connector->getConnection()->query("
        CREATE UNIQUE INDEX likes_article_id_IDX ON likes (article_id,user_id);
        ");
    }
}
