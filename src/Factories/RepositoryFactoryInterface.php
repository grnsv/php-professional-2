<?php

namespace App\Factories;

use App\Repositories\EntityRepositoryInterface;

interface RepositoryFactoryInterface
{
    public function create(string $entityType): EntityRepositoryInterface;
}
