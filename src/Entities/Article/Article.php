<?php

namespace App\Entities\Article;

use App\Entities\User\User;
use App\Traits\Id;

class Article implements ArticleInterface
{
    use Id;

    public function __construct(
        private User $author,
        private string $title,
        private string $text,
    ) {
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getTitle(): string
    {
        return $this->title;
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
            $this->getTitle(),
            $this->getText(),
        );
    }
}
