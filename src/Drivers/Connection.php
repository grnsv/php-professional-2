<?php

namespace App\Drivers;

use PDOStatement;

interface Connection
{
    public function prepare(string $query, array $options = []): PDOStatement|false;
    public function query(string $query, ?int $fetchMode = null): PDOStatement|false;
    public function beginTransaction(): bool;
    public function commit(): bool;
    public function rollBack(): bool;
    public function lastInsertId(string $name = null): string|false;
}
