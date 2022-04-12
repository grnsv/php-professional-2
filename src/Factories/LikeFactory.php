<?php

namespace App\Factories;

use App\Entities\Like\Like;
use JetBrains\PhpStorm\Pure;
use App\Container\DIContainer;
use App\Decorator\LikeDecorator;
use App\Repositories\UserRepository;
use App\Repositories\ArticleRepository;

final class LikeFactory implements LikeFactoryInterface
{
    #[Pure] public function create(LikeDecorator $likeDecorator): Like
    {
        /**
         * @var DIContainer $container
         */
        $container = DIContainer::getInstance();
        $userRepository = $container->get(UserRepository::class);
        $articleRepository = $container->get(ArticleRepository::class);
        $user = $userRepository->findById($likeDecorator->userId);
        $article = $articleRepository->findById($likeDecorator->articleId);
        return new Like(
            $user,
            $article,
        );
    }
}
