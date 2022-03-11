<?php

namespace App\Traits;

trait Id
{
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
