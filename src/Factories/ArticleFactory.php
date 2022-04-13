<?php

namespace App\Factories;

use JetBrains\PhpStorm\Pure;
use App\Container\DIContainer;
use App\Entities\Article\Article;
use App\Decorator\ArticleDecorator;
use App\Repositories\UserRepository;

final class ArticleFactory implements ArticleFactoryInterface
{
    #[Pure] public function create(ArticleDecorator $articleDecorator): Article
    {
        /**
         * @var DIContainer $container
         */
        $container = DIContainer::getInstance();
        $userRepository = $container->get(UserRepository::class);
        $author = $userRepository->findById($articleDecorator->authorId);
        return new Article(
            $author,
            $articleDecorator->title,
            $articleDecorator->text,
        );
    }
}
