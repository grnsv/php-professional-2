<?php

namespace App\Factories\Commands;

use App\Entities\Like\Like;
use App\Entities\User\User;
use App\Container\DIContainer;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
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
        $container = DIContainer::getInstance();
        return match ($entityType) {
            User::class => $container->get(CreateUserCommandHandler::class),
            Article::class => $container->get(CreateArticleCommandHandler::class),
            Comment::class => $container->get(CreateCommentCommandHandler::class),
            Like::class => $container->get(CreateLikeCommandHandler::class),
        };
    }
}
