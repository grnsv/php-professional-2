<?php

namespace App\Repositories;

use App\Entities\Like\Like;

interface LikeRepositoryInterface extends EntityRepositoryInterface
{
    public function get(int $id): Like;
    public function getByArticleId(int $articleId): array;
}
