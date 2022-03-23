<?php

namespace App\Connections;

use App\config\SqliteConfig;

class SqliteConnector extends Connector implements SqliteConnectorInterface
{
    public function getDsn(): string
    {
        return SqliteConfig::DSN;
    }

    public function getUserName(): string
    {
        return SqliteConfig::USER_NAME;
    }

    public function getPassword(): string
    {
        return SqliteConfig::PASSWORD;
    }

    public function getOptions(): array
    {
        return SqliteConfig::OPTIONS;
    }
}
