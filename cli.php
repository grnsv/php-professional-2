<?php

use App\Enums\Argument;
use App\Entities\User\User;
use App\Exceptions\NotFoundException;
use App\Factories\EntityManagerFactory;
use App\Exceptions\UserNotFoundException;
use App\Factories\EntityManagerFactoryInterface;

try {
    if (count($argv) < 2) {
        throw new NotFoundException('404');
    }

    if (!in_array($argv[1], Argument::getArgumentValues())) {
        throw new NotFoundException('404');
    }

    /**
     * @var EntityManagerFactoryInterface $entityMangerFactory
     */
    $entityMangerFactory = EntityManagerFactory::getInstance();
    $entity =  $entityMangerFactory->createEntityByInputArguments($argv);
    if ($entity instanceof User) {
        /**
         * @var UserRepositoryInterface $repository
         */
        $repository = $entityMangerFactory->getRepository($entity::class);

        try {
            $user = $repository->getUserByEmail($entity->getEmail());
        } catch (UserNotFoundException) {
            $entityMangerFactory->getEntityManager()->create($entity);
        }
    } else {
        $entityMangerFactory->getEntityManager()->create($entity);
    }
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
    http_response_code(404);
}
