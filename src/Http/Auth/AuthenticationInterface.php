<?php

namespace App\Http\Auth;

use App\Http\Request;
use App\Entities\User\User;

interface AuthenticationInterface
{
    public function getUser(Request $request): User;
}
