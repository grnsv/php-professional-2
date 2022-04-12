<?php

namespace App\Managers;

use App\Entities\EntityInterface;

interface EntityManagerInterface
{
    public function create(EntityInterface $entity): void;
}
