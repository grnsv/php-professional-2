<?php

namespace App\Factories;

use App\Entities\Like\Like;
use App\Entities\User\User;
use App\Container\DIContainer;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Repositories\LikeRepository;
use App\Repositories\UserRepository;
use App\Repositories\ArticleRepository;
use App\Repositories\CommentRepository;
use App\Repositories\EntityRepositoryInterface;

class RepositoryFactory implements RepositoryFactoryInterface
{
    public function create(string $entityType): EntityRepositoryInterface
    {
        $container = DIContainer::getInstance();
        return match ($entityType) {
            User::class => $container->get(UserRepository::class),
            Article::class => $container->get(ArticleRepository::class),
            Comment::class => $container->get(CommentRepository::class),
            Like::class => $container->get(LikeRepository::class),
        };
    }
}
