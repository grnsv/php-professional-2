<?php

namespace GeekBrains\User;

use DateTimeImmutable;

class User
{
    public function __construct(
        private int $id,
        private string $firstName,
        private string $lastName,
        private DateTimeImmutable $registeredOn
    ) {
    }

    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName .
            ' (на сайте с ' . $this->registeredOn->format('Y-m-d') . ')';
    }

    public function getId(): int
    {
        return $this->id;
    }
}
