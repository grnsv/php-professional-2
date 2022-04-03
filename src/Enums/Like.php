<?php

namespace App\Enums;

enum Like: string
{
    case ID = 'id';
    case USER_ID = 'userId';
    case ARTICLE_ID = 'articleId';

    public static function getRequiredFields(): array
    {
        return [
            Like::USER_ID->value,
            Like::ARTICLE_ID->value,
        ];
    }
}
