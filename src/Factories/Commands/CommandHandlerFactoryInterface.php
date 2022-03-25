<?php

namespace App\Factories\Commands;

use App\Commands\CommandHandlerInterface;

interface CommandHandlerFactoryInterface
{
    public function create(string $entityType): CommandHandlerInterface;
}
