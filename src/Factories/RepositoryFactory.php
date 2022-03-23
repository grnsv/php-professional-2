<?php

namespace App\Factories;

use App\Enums\Argument;
use JetBrains\PhpStorm\Pure;
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

    #[Pure] public function create(string $entityType): EntityRepositoryInterface
    {
        return match ($entityType) {
            Argument::USER->value => new UserRepository($this->connector),
            Argument::ARTICLE->value => new ArticleRepository($this->connector),
            Argument::COMMENT->value => new CommentRepository($this->connector),
        };
    }
}
