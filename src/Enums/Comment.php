<?php

namespace App\Enums;

enum Comment: string
{
    case ID = 'id';
    case AUTHOR_ID = 'authorId';
    case ARTICLE_ID = 'articleId';
    case TEXT = 'text';

    public static function getRequiredFields(): array
    {
        return [
            Comment::AUTHOR_ID->value,
            Comment::ARTICLE_ID->value,
            Comment::TEXT->value,
        ];
    }
}
