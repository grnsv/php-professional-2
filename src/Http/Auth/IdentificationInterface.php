<?php

namespace App\Http\Auth;

use App\Http\Request;
use App\Entities\User\User;

interface IdentificationInterface
{
    public function getUser(Request $request): User;
}
