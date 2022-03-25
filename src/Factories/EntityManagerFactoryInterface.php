<?php

namespace App\Factories;

use App\Entities\EntityInterface;
use App\Managers\EntityManagerInterface;
use App\Repositories\EntityRepositoryInterface;

interface EntityManagerFactoryInterface
{
    public function createEntity(string $entityType, array $arguments): EntityInterface;
    public function getRepository(string $entityType): EntityRepositoryInterface;
    public function createEntityByInputArguments(array $arguments): EntityInterface;
    public function getRepositoryByInputArguments(array $arguments): EntityRepositoryInterface;
    public function getEntityManager(): EntityManagerInterface;
}
