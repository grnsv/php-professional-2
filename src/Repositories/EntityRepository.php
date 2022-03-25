<?php

namespace App\Repositories;

use App\Drivers\Connection;
use App\Entities\EntityInterface;
use App\Connections\ConnectorInterface;

abstract class EntityRepository implements EntityRepositoryInterface
{
    protected Connection $connection;

    public function __construct(ConnectorInterface $connector)
    {
        $this->connection = $connector->getConnection();
    }

    abstract public function get(int  $id): EntityInterface;
}
