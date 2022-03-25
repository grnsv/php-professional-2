<?php

namespace App\Entities\Article;

use App\Entities\EntityInterface;
use App\Entities\User\UserInterface;

interface ArticleInterface extends EntityInterface
{
    public function getAuthor(): UserInterface;
    public function getTitle(): string;
    public function getText(): string;
}
