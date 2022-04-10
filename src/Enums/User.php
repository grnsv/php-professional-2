<?php

namespace App\Enums;

enum User: string
{
    case ID = 'id';
    case FIRST_NAME = 'firstName';
    case LAST_NAME = 'lastName';
    case EMAIL = 'email';
    case PASSWORD = 'password';

    public static function getRequiredFields(): array
    {
        return [
            User::FIRST_NAME->value,
            User::LAST_NAME->value,
            User::EMAIL->value,
            User::PASSWORD->value,
        ];
    }
}
