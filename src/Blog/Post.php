<?php

namespace GeekBrains\Blog;

use GeekBrains\User\User;

class Post
{
    private int $authorId;

    public function __construct(
        private int $id,
        private User $author,
        private string $title,
        private string $text
    ) {
        $this->authorId = $author->getId();
    }

    public function __toString()
    {
        return sprintf(
            "%s (ID: %d) пишет:%s%s >>>%s%s",
            $this->author,
            $this->authorId,
            PHP_EOL,
            $this->title,
            PHP_EOL,
            $this->text,
        );
    }

    public function getId(): int
    {
        return $this->id;
    }
}