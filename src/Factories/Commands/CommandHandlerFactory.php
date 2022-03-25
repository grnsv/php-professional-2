<?php

namespace App\Factories\Commands;

use App\Entities\User\User;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Factories\EntityManagerFactory;
use App\Commands\CommandHandlerInterface;
use App\Commands\CreateUserCommandHandler;
use App\Commands\CreateArticleCommandHandler;
use App\Commands\CreateCommentCommandHandler;

class CommandHandlerFactory implements CommandHandlerFactoryInterface
{
    public function create(string $entityType): CommandHandlerInterface
    {
        return match ($entityType) {
            User::class => new CreateUserCommandHandler(EntityManagerFactory::getInstance()->getRepository(User::class)),
            Article::class => new CreateArticleCommandHandler(),
            Comment::class => new CreateCommentCommandHandler(),
        };
    }
}
