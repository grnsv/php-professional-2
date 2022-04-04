<?php

use App\Enums\Argument;
use App\Entities\Like\Like;
use App\Entities\User\User;
use Psr\Log\LoggerInterface;
use App\Container\DIContainer;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Commands\CreateEntityCommand;
use App\Exceptions\NotFoundException;
use App\Factories\EntityManagerFactory;
use App\Commands\CommandHandlerInterface;
use App\Commands\CreateLikeCommandHandler;
use App\Commands\CreateUserCommandHandler;
use App\Commands\CreateArticleCommandHandler;
use App\Commands\CreateCommentCommandHandler;
use App\Factories\EntityManagerFactoryInterface;

/**
 * @var DIContainer $container
 */
if (isset($container)) {
    /**
     * @var LoggerInterface $logger
     */
    $logger = $container->get(LoggerInterface::class);

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
        $entity = $entityMangerFactory->createEntityByInputArguments($argv);

        /**
         * @var CommandHandlerInterface $commandHandler
         */
        $commandHandler =  match ($entity::class) {
            Article::class => $container->get(CreateArticleCommandHandler::class),
            Comment::class => $container->get(CreateCommentCommandHandler::class),
            User::class => $container->get(CreateUserCommandHandler::class),
            Like::class => $container->get(CreateLikeCommandHandler::class),
        };

        $commandHandler->handle(new CreateEntityCommand($entity));
    } catch (Exception $exception) {
        $logger->error($exception->getMessage(), ['exception' => $exception]);

        echo $exception->getMessage() . PHP_EOL;
        http_response_code(404);
    }
}
