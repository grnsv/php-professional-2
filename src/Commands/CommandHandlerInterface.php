<?php

namespace App\Commands;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): void;
    public function getSQL(): string;
}
