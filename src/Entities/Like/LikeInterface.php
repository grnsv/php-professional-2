<?php

namespace App\Entities\Like;

use App\Entities\EntityInterface;
use App\Entities\User\UserInterface;
use App\Entities\Article\ArticleInterface;

interface LikeInterface extends EntityInterface
{
    public function getUser(): UserInterface;
    public function getArticle(): ArticleInterface;
}
