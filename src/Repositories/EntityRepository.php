<?php

namespace App\Repositories;

use App\Drivers\Connection;
use App\Entities\EntityInterface;

abstract class EntityRepository implements EntityRepositoryInterface
{
    public function __construct(protected Connection $connection)
    {
    }

    abstract public function get(int $id): EntityInterface;
    abstract public function isExists(int $id): bool;
}
