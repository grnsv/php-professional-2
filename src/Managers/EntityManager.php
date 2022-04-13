<?php

namespace App\Managers;

use App\Commands\EntityCommand;
use App\Entities\EntityInterface;
use App\Factories\Commands\CommandHandlerFactory;
use App\Factories\Commands\CommandHandlerFactoryInterface;

class EntityManager implements EntityManagerInterface
{
    private CommandHandlerFactoryInterface $commandHandlerFactory;

    public function __construct(
        CommandHandlerFactoryInterface $commandHandlerFactory = null
    ) {
        $this->commandHandlerFactory = $commandHandlerFactory ?? new CommandHandlerFactory();
    }

    public function create(EntityInterface $entity): void
    {
        $commandHandler = $this->commandHandlerFactory->create($entity::class);
        $commandHandler->handle(new EntityCommand($entity));
    }
}
