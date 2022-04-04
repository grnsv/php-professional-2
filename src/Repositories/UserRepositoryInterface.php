<?php

namespace App\Repositories;

use App\Entities\User\User;

interface UserRepositoryInterface extends EntityRepositoryInterface
{
    public function get(int $id): User;
    public function getUserByEmail(string $email): User;
    public function isUserExists(string $email): bool;
}
