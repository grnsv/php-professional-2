<?php

namespace App\Factories\Commands;

use App\Entities\Like\Like;
use App\Entities\User\User;
use Psr\Log\LoggerInterface;
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
        $connection = $container->get(Connection::class);
        $loggerInterface = $container->get(LoggerInterface::class);
        return match ($entityType) {
            User::class => new CreateUserCommandHandler(
                $container->get(UserRepository::class),
                $connection,
                $loggerInterface,
            ),
            Article::class => new CreateArticleCommandHandler(
                $container->get(ArticleRepository::class),
                $connection,
                $loggerInterface,
            ),
            Comment::class => new CreateCommentCommandHandler(
                $container->get(CommentRepository::class),
                $connection,
                $loggerInterface,
            ),
            Like::class => new CreateLikeCommandHandler(
                $container->get(LikeRepository::class),
                $connection,
                $loggerInterface,
            ),
        };
    }
}
