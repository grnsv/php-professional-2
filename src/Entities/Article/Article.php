<?php

namespace App\Entities\Article;

use App\Traits\Id;
use App\Traits\Text;
use App\Traits\Title;
use App\Traits\Author;
use App\Entities\User\User;

class Article implements ArticleInterface
{
    use Id;
    use Author;
    use Title;
    use Text;

    public const TABLE_NAME = 'articles';

    public function __construct(
        User $author,
        string $title,
        string $text,
    ) {
        $this->author = $author;
        $this->title = $title;
        $this->text = $text;
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

    public function getTableName(): string
    {
        return static::TABLE_NAME;
    }
}
