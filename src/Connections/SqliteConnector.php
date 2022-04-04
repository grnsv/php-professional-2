<?php

namespace App\Connections;

class SqliteConnector extends Connector implements SqliteConnectorInterface
{
    public function getDsn(): string
    {
        return $_SERVER['DSN_DATABASE'];
    }

    public function getUserName(): string
    {
        return '';
    }

    public function getPassword(): string
    {
        return '';
    }

    public function getOptions(): array
    {
        return [];
    }
}
