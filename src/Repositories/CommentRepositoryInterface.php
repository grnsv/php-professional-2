<?php

namespace App\Repositories;

use App\Entities\Comment\Comment;

interface CommentRepositoryInterface extends EntityRepositoryInterface
{
    public function findById(int $id): Comment;
}
