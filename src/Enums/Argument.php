<?php

namespace App\Enums;

enum Argument: string
{
    case USER = 'user';
    case ARTICLE = 'article';
    case COMMENT = 'comment';
    case LIKE = 'like';

    public const ARGUMENTS = [
        Argument::USER,
        Argument::ARTICLE,
        Argument::COMMENT,
        Argument::LIKE,
    ];

    public static function getArgumentValues(): array
    {
        return [
            Argument::USER->value,
            Argument::ARTICLE->value,
            Argument::COMMENT->value,
            Argument::LIKE->value,
        ];
    }
}
