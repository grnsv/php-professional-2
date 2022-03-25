<?php

namespace App\Commands;

use App\Entities\EntityInterface;

class CreateEntityCommand implements CommandInterface
{
    public function __construct(private EntityInterface $entity)
    {
    }

    public function getEntity(): EntityInterface
    {
        return $this->entity;
    }
}
