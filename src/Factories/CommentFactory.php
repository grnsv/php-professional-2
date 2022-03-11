<?php

namespace App\Factories;

use JetBrains\PhpStorm\Pure;
use App\Entities\Comment\Comment;
use App\Decorator\CommentDecorator;


final class CommentFactory implements CommentFactoryInterface
{
    #[Pure] public function create(CommentDecorator $commentDecorator): Comment
    {
        return new Comment(
            $commentDecorator->author,
            $commentDecorator->article,
            $commentDecorator->text,
        );
    }
}
