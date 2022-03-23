<?php

namespace App\Entities\Comment;

use App\Entities\EntityInterface;
use App\Entities\User\UserInterface;
use App\Entities\Article\ArticleInterface;

interface CommentInterface extends EntityInterface
{
    public function getAuthor(): UserInterface;
    public function getArticle(): ArticleInterface;
    public function getText(): string;
}
