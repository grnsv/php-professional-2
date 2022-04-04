<?php

namespace App\Repositories;

use App\Entities\Article\Article;

interface ArticleRepositoryInterface extends EntityRepositoryInterface
{
    public function get(int $id): Article;
}
