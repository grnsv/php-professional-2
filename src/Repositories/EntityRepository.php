<?php

namespace App\Repositories;

use App\Entities\EntityInterface;
use App\Connections\ConnectorInterface;

abstract class EntityRepository implements EntityRepositoryInterface
{
    protected ConnectorInterface $connector;

    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    abstract public function save(EntityInterface $entity): void;
    abstract public function get(int  $id): EntityInterface;
}
