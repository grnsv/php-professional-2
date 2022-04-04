<?php

namespace App\Entities\Like;

use App\Traits\Id;
use App\Entities\User\User;
use App\Entities\Article\Article;

class Like implements LikeInterface
{
    use Id;

    public const TABLE_NAME = 'likes';

    public function __construct(
        private User $user,
        private Article $article,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getArticle(): Article
    {
        return $this->article;
    }

    public function __toString(): string
    {
        return sprintf(
            "[%d] %s %s %s",
            $this->getId(),
            $this->getUser(),
            $this->getArticle(),
        );
    }

    public function getTableName(): string
    {
        return static::TABLE_NAME;
    }
}
