<?php

namespace App\Repositories;

use App\Entities\EntityInterface;

interface EntityRepositoryInterface
{
    public function get(int $id): EntityInterface;
    public function isExists(int $id): bool;
}
