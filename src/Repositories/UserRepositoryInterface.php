<?php

namespace App\Repositories;

use App\Entities\User\User;

interface UserRepositoryInterface extends EntityRepositoryInterface
{
    public function getUserByEmail(string $email): User;
}
