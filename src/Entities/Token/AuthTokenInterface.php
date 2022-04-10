<?php

namespace App\Entities\Token;

use DateTimeImmutable;
use App\Entities\User\User;

interface AuthTokenInterface
{
    public function getToken(): string;
    public function getUser(): User;
    public function getExpiresOn(): DateTimeImmutable;
    public function isExpires(): bool;
    public function __toString(): string;
    public function __serialize(): array;
}
