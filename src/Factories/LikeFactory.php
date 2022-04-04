<?php

namespace App\Factories;

use App\Entities\Like\Like;
use App\Entities\User\User;
use App\Commands\GetCommand;
use JetBrains\PhpStorm\Pure;
use App\Decorator\LikeDecorator;
use App\Entities\Article\Article;

final class LikeFactory implements LikeFactoryInterface
{
    #[Pure] public function create(LikeDecorator $likeDecorator): Like
    {
        /**
         * @var EntityManagerFactoryInterface $entityMangerFactory
         */
        $entityMangerFactory = EntityManagerFactory::getInstance();
        $command = new GetCommand($entityMangerFactory->getRepository(User::class));
        $user = $command->handle($likeDecorator->userId);
        $command = new GetCommand($entityMangerFactory->getRepository(Article::class));
        $article = $command->handle($likeDecorator->articleId);
        return new Like(
            $user,
            $article,
        );
    }
}
