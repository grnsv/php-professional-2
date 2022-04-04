<?php

namespace App\Entities\Comment;

use App\Traits\Id;
use App\Traits\Text;
use App\Traits\Author;
use App\Entities\User\User;
use App\Entities\Article\Article;
use App\Traits\Article as TraitsArticle;

class Comment implements CommentInterface
{
    use Id;
    use Author;
    use TraitsArticle;
    use Text;

    public const TABLE_NAME = 'comments';

    public function __construct(
        User $author,
        Article $article,
        string $text,
    ) {
        $this->author = $author;
        $this->article = $article;
        $this->text = $text;
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

    public function getTableName(): string
    {
        return static::TABLE_NAME;
    }
}
