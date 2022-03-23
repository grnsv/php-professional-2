<?php

namespace App\Factories;

use App\Entities\User\User;
use JetBrains\PhpStorm\Pure;
use App\Decorator\UserDecorator;

final class UserFactory implements UserFactoryInterface
{
    #[Pure] public function create(UserDecorator $userDecorator): User
    {
        return new User(
            $userDecorator->firstName,
            $userDecorator->lastName,
            $userDecorator->email,
        );
    }
}
