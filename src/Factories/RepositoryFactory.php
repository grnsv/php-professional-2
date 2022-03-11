<?php

namespace App\Factories;

use App\Entities\User\User;
use JetBrains\PhpStorm\Pure;
use App\Entities\EntityInterface;
use App\Connections\SqliteConnector;
use App\Repositories\UserRepository;
use App\Connections\ConnectorInterface;
use App\Repositories\ArticleRepository;
use App\Repositories\CommentRepository;
use App\Repositories\EntityRepositoryInterface;

class RepositoryFactory implements RepositoryFactoryInterface
{
    private ConnectorInterface $connector;

    #[Pure] public function __construct(ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new SqliteConnector();
    }

    #[Pure] public function create(EntityInterface $entity): EntityRepositoryInterface
    {
        return match ($entity::class) {
            User::class => new UserRepository($this->connector),
            Article::class => new ArticleRepository($this->connector),
            Comment::class => new CommentRepository($this->connector),
        };
    }
}
