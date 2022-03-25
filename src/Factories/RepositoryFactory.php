<?php

namespace App\Factories;

use App\Entities\User\User;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Connections\SqliteConnector;
use App\Repositories\UserRepository;
use App\Connections\ConnectorInterface;
use App\Repositories\ArticleRepository;
use App\Repositories\CommentRepository;
use App\Repositories\EntityRepositoryInterface;

class RepositoryFactory implements RepositoryFactoryInterface
{
    private ConnectorInterface $connector;

    public function __construct(ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new SqliteConnector();
    }

    public function create(string $entityType): EntityRepositoryInterface
    {
        return match ($entityType) {
            User::class => new UserRepository($this->connector),
            Article::class => new ArticleRepository($this->connector),
            Comment::class => new CommentRepository($this->connector),
        };
    }
}
