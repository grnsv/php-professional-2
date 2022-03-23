<?php

namespace App\Commands;

use App\Entities\EntityInterface;
use App\Repositories\EntityRepositoryInterface;

class GetCommand
{
    public function __construct(
        private EntityRepositoryInterface $entityRepository,
    ) {
    }

    public function handle(int $id): EntityInterface
    {
        return $this->entityRepository->get($id);
    }
}
