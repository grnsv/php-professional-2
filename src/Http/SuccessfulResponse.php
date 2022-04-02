<?php

declare(strict_types=1);

namespace App\Http;

class SuccessfulResponse extends Response
{
    public function __construct(private array $data = [])
    {
    }

    protected function payload(): array
    {
        return ['data' => $this->data];
    }
}
