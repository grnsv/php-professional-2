<?php

namespace GeekBrains\Blog;

use GeekBrains\User\User;
use GeekBrains\Blog\Post;

class Comment
{
    private int $authorId;
    private int $postId;

    public function __construct(
        private int $id,
        private User $author,
        private Post $post,
        private string $text
    ) {
        $this->authorId = $author->getId();
        $this->postId = $post->getId();
    }

    public function __toString()
    {
        return $this->author . ' (ID: ' . $this->authorId . ') пишет: ' . PHP_EOL .
            $this->text;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
