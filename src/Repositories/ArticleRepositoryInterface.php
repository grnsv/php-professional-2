<?php

namespace App\Repositories;

use App\Entities\Article\Article;

interface ArticleRepositoryInterface extends EntityRepositoryInterface
{
    public function findById(int $id): Article;
}
