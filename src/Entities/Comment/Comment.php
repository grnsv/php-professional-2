<?php

namespace App\Entities\Comment;

use App\Entities\User\User;
use App\Entities\Article\Article;
use App\Traits\Id;

class Comment implements CommentInterface
{
    use Id;

    public function __construct(
        private User $author,
        private Article $article,
        private string $text,
    ) {
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getArticle(): Article
    {
        return $this->article;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function __toString(): string
    {
        return sprintf(
            "[%d] %s %s %s",
            $this->getId(),
            $this->getAuthor(),
            $this->getArticle(),
            $this->getText(),
        );
    }
}
