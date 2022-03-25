<?php

namespace App\Enums;

enum Article: string
{
    case ID = 'id';
    case AUTHOR_ID = 'authorId';
    case TITLE = 'title';
    case TEXT = 'text';

    public static function getRequiredFields(): array
    {
        return [
            Article::AUTHOR_ID->value,
            Article::TITLE->value,
            Article::TEXT->value,
        ];
    }
}
