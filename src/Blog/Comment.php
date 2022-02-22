<?php

namespace GeekBrains\Blog;

use GeekBrains\User\User;
use GeekBrains\Blog\Post;

class Comment
{
    private int $author_id;
    private int $post_id;

    public function __construct(
        private int $id,
        private User $author,
        private Post $post,
        private string $text
    ) {
        $this->author_id = $author->getId();
        $this->post_id = $post->getId();
    }

    public function __toString()
    {
        return $this->author . ' (ID: ' . $this->author_id . ') пишет: ' . PHP_EOL .
            $this->text;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
