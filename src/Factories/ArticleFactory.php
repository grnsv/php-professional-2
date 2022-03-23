<?php

namespace App\Factories;

use App\Commands\GetCommand;
use JetBrains\PhpStorm\Pure;
use App\Entities\Article\Article;
use App\Decorator\ArticleDecorator;

final class ArticleFactory implements ArticleFactoryInterface
{
    #[Pure] public function create(ArticleDecorator $articleDecorator): Article
    {
        /**
         * @var EntityManagerFactoryInterface $entityManager
         */
        $entityManager = EntityManagerFactory::getInstance();
        $command = new GetCommand($entityManager->getRepository('user'));
        $author = $command->handle($articleDecorator->authorId);
        return new Article(
            $author,
            $articleDecorator->title,
            $articleDecorator->text,
        );
    }
}
