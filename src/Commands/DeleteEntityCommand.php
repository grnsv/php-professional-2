<?php

namespace App\Commands;

class DeleteEntityCommand implements CommandInterface
{
    public function __construct(private int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
