<?php

namespace App\Entities\Article;

use App\Entities\User\UserInterface;
use App\Entities\EntityInterface;

interface ArticleInterface extends EntityInterface
{
    public function getAuthor(): UserInterface;
    public function getTitle(): string;
    public function getText(): string;
}
