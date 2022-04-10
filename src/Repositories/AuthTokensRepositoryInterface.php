<?php

namespace App\Repositories;


use App\Entities\User\User;
use App\Entities\Token\AuthToken;

interface AuthTokensRepositoryInterface
{
    public function getToken(string $token): ?AuthToken;
    public function getTokenByUser(User $user): ?AuthToken;
}
