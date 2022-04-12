<?php

namespace App\Commands;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command);
    public function getSQL(): string;
}
