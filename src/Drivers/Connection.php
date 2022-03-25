<?php

namespace App\Drivers;

interface Connection
{
    public function executeQuery(string $query, array $params);
    public function prepare(string $query, array $options = []);
}
