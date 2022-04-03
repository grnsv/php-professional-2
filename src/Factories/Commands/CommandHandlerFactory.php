<?php

namespace App\Factories\Commands;

use App\Entities\Like\Like;
use App\Entities\User\User;
use App\Container\DIContainer;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Repositories\UserRepository;
use App\Commands\CommandHandlerInterface;
use App\Commands\CreateLikeCommandHandler;
use App\Commands\CreateUserCommandHandler;
use App\Commands\CreateArticleCommandHandler;
use App\Commands\CreateCommentCommandHandler;

class CommandHandlerFactory implements CommandHandlerFactoryInterface
{
    public function create(string $entityType): CommandHandlerInterface
    {
        /**
         * @var DIContainer $container
         */
        return match ($entityType) {
            User::class => new CreateUserCommandHandler($container->get(UserRepository::class), $container->get(Connection::class)),
            Article::class => new CreateArticleCommandHandler($container->get(ArticleRepository::class), $container->get(Connection::class)),
            Comment::class => new CreateCommentCommandHandler($container->get(CommentRepository::class), $container->get(Connection::class)),
            Like::class => new CreateLikeCommandHandler($container->get(LikeRepository::class), $container->get(Connection::class)),
        };
    }
}
