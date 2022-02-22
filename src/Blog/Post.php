<?php

namespace GeekBrains\Blog;

use GeekBrains\User\User;

class Post
{
    private int $author_id;

    public function __construct(
        private int $id,
        private User $author,
        private string $title,
        private string $text
    ) {
        $this->author_id = $author->getId();
    }

    public function __toString()
    {
        return $this->author . ' (ID: ' . $this->author_id . ') пишет: ' . PHP_EOL .
            $this->title . ' >>> ' . $this->text;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
